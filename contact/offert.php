<?php
/**
 * Södra Låsteknik – mottagare för offertförfrågan.
 *
 * Tar emot POST från contact/index.html, inklusive bifogade bilder/ritningar, och
 * mejlar förfrågan till info@sodralasteknik.se med filerna som bilagor.
 *
 * Filerna sparas ALDRIG på servern. PHP lägger uppladdningen i en temp-fil,
 * vi läser in den, bygger ett MIME-mejl och skickar. När skriptet är slut
 * raderar PHP temp-filen automatiskt. Inget blir kvar på webbhotellet.
 *
 * Kräver PHP på hosten (One.com har det). Avsändaren (From) sätts till en adress
 * på egen domän så att mejlet passerar SPF; kundens adress hamnar i Reply-To.
 *
 * OBS – One.com: den här koden bär med sig två fixar som krävdes på Loopia:
 *   (1) envelope-avsändare via -f, och (2) ett fullständigt 7-bitars rent
 *   meddelande (MIME-kodade headers + base64-brödtext) för att undvika SMTPUTF8-studs.
 * Mejlleverans via mail() kan bete sig annorlunda på One.com – TESTA SKARPT.
 * Om mejlet inte landar är reservlösningen autentiserad SMTP via PHPMailer mot
 * One.coms SMTP-server. Se PROJEKT.md §6.
 */

// ===== Konfiguration =====
$TO       = 'info@sodralasteknik.se';   // Vart förfrågningar skickas
$FROM     = 'info@sodralasteknik.se';   // Måste vara en adress på sodralasteknik.se
$MAX_FILE = 5 * 1024 * 1024;            // Max 5 MB per fil
$MAX_TOT  = 15 * 1024 * 1024;           // Max 15 MB totalt (mejlservrar stryper över det)
$OK_TYPES = ['image/jpeg', 'image/png', 'image/heic', 'image/webp', 'application/pdf'];

header('Content-Type: application/json; charset=utf-8');

function fail($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fail('Endast POST tillåts.', 405);
}

// ===== Honeypot =====
if (!empty($_POST['botcheck'])) {
    echo json_encode(['ok' => true]);  // Låtsas lyckat så boten inte försöker igen
    exit;
}

function field($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

$name    = field('name');
$company = field('company');
$email   = field('email');
$phone   = field('phone');
$type    = field('type');
$message = field('message');

// ===== Validering =====
if ($name === '' || $company === '' || $email === '' || $phone === '' || $message === '') {
    fail('Fyll i namn, företag, e-post, telefon och beskrivning.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail('Ange en giltig e-postadress.');
}
if (mb_strlen($message) > 5000) {
    fail('Beskrivningen är för lång.');
}

// ===== Skydd mot header-injektion =====
foreach (['name', 'company', 'email', 'phone'] as $k) {
    $$k = str_replace(["\r", "\n"], '', $$k);
}

// ===== Samla in bilagor =====
$attachments = [];
$total = 0;

if (!empty($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
    $count = count($_FILES['attachments']['name']);
    for ($i = 0; $i < $count; $i++) {
        if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
        if ($_FILES['attachments']['error'][$i] !== UPLOAD_ERR_OK) {
            fail('Något gick fel vid uppladdningen av en fil.');
        }

        $tmp  = $_FILES['attachments']['tmp_name'][$i];
        $size = $_FILES['attachments']['size'][$i];

        if ($size > $MAX_FILE) fail('Varje fil får vara högst 5 MB.');
        $total += $size;
        if ($total > $MAX_TOT) fail('Filerna är tillsammans för stora (max 15 MB).');

        // Lita på filens faktiska innehåll, inte på vad webbläsaren påstår
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $filetype = finfo_file($finfo, $tmp);
        finfo_close($finfo);
        if (!in_array($filetype, $OK_TYPES, true)) {
            fail('Endast bilder (JPG, PNG, HEIC, WEBP) och PDF kan bifogas.');
        }

        // Egen filnamnssanering – webbläsarens namn får aldrig gå rakt in i mejlet
        $fname = basename($_FILES['attachments']['name'][$i]);
        $fname = preg_replace('/[^\w.\- ]+/u', '_', $fname);
        if ($fname === '' || $fname === null) $fname = 'bilaga-' . ($i + 1);

        $attachments[] = [
            'name' => $fname,
            'type' => $filetype,
            'data' => file_get_contents($tmp),
        ];
    }
}

// ===== Bygg mejltexten =====
$body  = "Ny offertförfrågan via sodralasteknik.se\n";
$body .= "========================================\n";
$body .= "Namn:              $name\n";
$body .= "Företag:           $company\n";
$body .= "E-post:            $email\n";
$body .= "Telefon:           $phone\n";
$body .= "Typ av uppdrag:    " . ($type !== '' ? $type : '-') . "\n";
$body .= "Bifogade filer:    " . count($attachments) . "\n";
$body .= "========================================\n\n";
$body .= "Beskrivning av uppdraget:\n$message\n";

// MIME-koda ett headervärde som kan innehålla svenska tecken (encoded-word).
// Utan detta hamnar rå UTF-8 i headern, vilket kan få mejlservern att kräva
// SMTPUTF8 – som en del interna relän inte stödjer, så mejlet studsar. Endast
// ren ASCII lämnas orörd så att adresser och radbrytningar inte förstörs.
function mimeword($text) {
    if (preg_match('/[^\x20-\x7E]/', $text)) {
        return '=?UTF-8?B?' . base64_encode($text) . '?=';
    }
    return $text;
}

$subjectText = 'Offertförfrågan: ' . $company;
$subject = mimeword($subjectText);

// ===== Sätt ihop MIME-meddelandet =====
// Hela meddelandet hålls 7-bitars rent: headers MIME-kodas och brödtexten
// base64-kodas. Då finns ingen rå UTF-8 kvar och SMTPUTF8 triggas aldrig.
$boundary = '=_sodra_' . bin2hex(random_bytes(12));

$headers  = 'From: ' . mimeword('Södra Låsteknik') . " <$FROM>\r\n";
$headers .= 'Reply-To: ' . mimeword($name) . " <$email>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
$headers .= "X-Mailer: sodralasteknik.se\r\n";

$mail  = "--$boundary\r\n";
$mail .= "Content-Type: text/plain; charset=UTF-8\r\n";
$mail .= "Content-Transfer-Encoding: base64\r\n\r\n";
$mail .= chunk_split(base64_encode($body)) . "\r\n";

foreach ($attachments as $file) {
    $fn = mimeword($file['name']);
    $mail .= "--$boundary\r\n";
    $mail .= "Content-Type: {$file['type']}; name=\"$fn\"\r\n";
    $mail .= "Content-Transfer-Encoding: base64\r\n";
    $mail .= "Content-Disposition: attachment; filename=\"$fn\"\r\n\r\n";
    $mail .= chunk_split(base64_encode($file['data'])) . "\r\n";
}
$mail .= "--$boundary--";

// ===== Skicka =====
// Femte parametern sätter avsändarkuvertet (envelope-from) via sendmails -f-flagga.
// Utan den blir kuvertavsändaren serverns systemanvändare (t.ex. www-data@server),
// vilket inte matchar sodralasteknik.se, faller på SPF och gör att mejlet tyst
// slängs trots att mail() returnerar true. Adressen måste ligga på egen domän.
if (mail($TO, $subject, $mail, $headers, '-f ' . $FROM)) {
    echo json_encode(['ok' => true]);
} else {
    fail('Kunde inte skicka förfrågan just nu.', 500);
}
