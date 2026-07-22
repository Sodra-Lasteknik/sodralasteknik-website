# Södra Låsteknik — hemsida

Arbetsdokument. Vad vi vet, vad vi beslutat, vad som är gjort och vad som återstår.
Öppna det här repot (`C:\git\sodralasteknik-website`) för att fortsätta bygga — allt
som behövs finns här, inget beror på Nordö-projektet.

**Status 2026-07-21: FÄRDIG LOKALT.** Alla sidor byggda: startsida, kontakt-/offertsida med
PHP-formulär, integritetspolicy, 404, samt robots/sitemap/manifest. Alla svarar 200 lokalt.
Kvar innan lansering: **deploy till One.com** (SFTP-secrets + webbrot) och **skarpt mejltest av
offertformuläret** (PHP saknas lokalt, mejlleveransen måste verifieras på One.com – se §6).
Ev. og-image. Ingenting är publicerat än.

---

## 1. Uppdraget

Hemsida åt en väns bolag, **Södra Låsteknik AB** — en låssmed i Stockholm som främst
jobbar B2B (byggföretag, fastighetsägare, bostadsrättsföreningar). Byggs av Daniel Sjöholm
som vänsktjänst. Samma princip som Nordö-sajten: statisk, överlämningsbar, inga beroenden
som kräver löpande underhåll. Endast svenska.

## 2. Bolagsfakta

Bekräftat av kunden 2026-07-21 (formulär).

| | |
|---|---|
| Bolagsnamn | Södra Låsteknik AB |
| Org.nr | 559391-6835 |
| Säte | Stockholm |
| Domän | sodralasteknik.se (befintlig, ligger hos **One.com**) |
| E-post | info@sodralasteknik.se |
| Telefon (växel) | 08-19 37 67 |
| Journummer | Finns inte — ingen jour/utryckning i nuläget |
| Butik | Nej |
| Område | Hela Sverige, med Stockholm som bas |
| Kundtyper | Främst byggföretag (nybyggnation, ROT), fastighetsägare, BRF. Sällan privatpersoner. |
| Betalning | 100 % faktura (B2B) |
| Öppettider | Inga fasta ännu; föreslaget 08–17 |

**Tjänster (kryssade i formuläret):** Låsbyte & montering, Cylinderbyte, Säkerhetsdörrar,
Lås & beslag, Passersystem, Kassaskåp & värdeskåp, Fastighets- & portlås,
Säkerhetsbesiktning & rådgivning, Låsprojektering.
*(Ej kryssade, alltså EJ med: akut låsöppning, nyckeltillverkning, larm/kameraövervakning,
låssmedsjour dygnet runt.)*

**Förtroende:** 2 mästarbrev inom lås (branschens högsta kvalitetsstämpel), medlem i
**Installatörsföretagen (IN)**. Inga SSF-certifieringar eller SLR-medlemskap i nuläget.
Godkända försäkringar som täcker entreprenaderna.

**Varumärken de jobbar med:** ASSA, Step, Safetron, Dormakaba, EVVA, Salto, Axema.

**Bakgrund:** Södra Låsteknik ca 3 år gammalt, 3 anställda, växande omsättning. Över 50 års
samlad erfarenhet. Thomas Östling och Markus — låssmeder sedan gymnasiet, båda med mästarbrev.
Jesper — 18 år inom lås/försäljning, började på en låssmedsgrossist.

**Primär handling på sajten:** både **"Ring nu"** och **"Begär offert"**. Offertförfrågan ska
kunna innehålla **bifogade bilder/ritningar**.

## 3. Teknik- och designbeslut

- **Statisk sajt, återanvänd Nordö-grunden.** Vanilla HTML/CSS/JS, inget byggsteg, samma
  tokens-approach och samma JS (`main.js`, `contact.js`) som nordofast.se. Ny identitet.
- **Designsystem:** `assets/css/tokens.css`, prefix **`--sl-`**. Palett samplad ur loggan —
  grön `#387838` (`--sl-brand`), grå `#808080`, nära-svart text, vitt/ljusgrått som bakgrund.
  Mörk grön-svart för hero och paneler. Stramare radier än Nordö (teknisk känsla).
- **Typsnitt:** Space Grotesk (rubriker) + Inter (brödtext), från Google Fonts.
- **Ett tema** (inget `prefers-color-scheme`).
- **~~Fotofri design~~ → egna foton (2026-07-22).** Kunden har tagit fram egna bilder. `hero.png`
  (de två profilerade servicebilarna framför lokalen) är nu mörk hero-bakgrund med läsbarhets-
  overlay på ALLA sidor (`.hero-dark`, `.page-hero`, `.legal-hero`). `om-oss.png` (låskista + nycklar)
  fyller om-oss-boxen och `varfor-oss.png` (hänglås med emblem) fyller varför-oss-boxen. Båda
  boxarna har ett **kort citat överlagrat i nederkant** (mörk toning för läsbarhet, ingen grön box).
  De gamla emblem-vattenstämplarna är pensionerade. Nya hero-foton bör vara relativt mörka, annars måste
  overlay-opaciteten höjas för att texten ska vara läsbar.
- **Hosting: One.com** (inte Loopia). Domänen ligger där. One.com använder **SFTP**.
- **Offertformulär via PHP** med bildbilagor — samma mönster som Nordös serviceanmälan
  (`serviceanmalan.php`), som är verifierat och härdat. Web3Forms gratisplan stödjer inte
  filuppladdning, därför PHP. **OBS:** mejlfixarna som krävdes på Loopia (envelope-`-f`,
  7-bitars MIME) kan behöva verifieras/anpassas på One.com — se avsnitt 6.

## 4. Vad som är GJORT

- [x] Eget git-repo initierat (`git init`, gren `main`).
- [x] Nordö-grundens återanvändbara filer kopierade: `assets/js/main.js`, `assets/js/contact.js`,
      `serve.py`, `.gitignore`.
- [x] Loggor på plats: `assets/img/logo.png` (full lockup), `logo-text.png` (ordbild),
      `emblem.png` (sköld), samt `favicon.png` (tight-beskuret emblem, genererat).
- [x] `assets/css/tokens.css` — komplett Södra-palett och typografi (`--sl-`-prefix).
- [x] `assets/css/main.css` — Nordös main.css ombrandad: prefix `--n-`→`--sl-`, guld→grön,
      cream→vitt, skogsgröna overlays→mörk grön-svart. Plus Södra-specifik CSS-sektion sist
      (fotofri hero, varumärkes-chips, why-/about-paneler, 3-kolumns tjänsteruta).
- [x] `index.html` — komplett startsida: nav (med telefon + "Begär offert"), fotofri hero,
      nyckelfakta-band, 9 tjänstekort, "Varför oss", varumärkessektion, "Om oss", CTA, footer.
      JSON-LD `Locksmith` med bolagsfakta.
- [x] `.github/workflows/deploy.yml` — omskriven för One.com/SFTP (med TODO för secrets + webbrot).
- [x] `contact/index.html` — kontakt-/offertsida: nav/footer i synk med startsidan, page-hero,
      fyra kontaktkort (telefon, mejl, område, "så jobbar vi"), offertformulär (namn, företag,
      e-post, telefon, typ av uppdrag, beskrivning, **bifoga bilder/ritningar**) + honeypot,
      och en 6-frågors FAQ anpassad för Södra (B2B, ingen jour, faktura, hela Sverige).
- [x] `contact/offert.php` — härdad PHP-mottagare kopierad från Nordös `serviceanmalan.php`.
      Mottagare info@sodralasteknik.se, fält anpassade (namn/företag/e-post/telefon/typ/meddelande),
      `attachments[]`-bilagor. Behåller `finfo`-validering, honeypot, envelope-`-f` och 7-bitars
      MIME. **Ej testad skarpt** — verifiera mejl på One.com (§6).
- [x] `assets/js/contact.js` — ombrandad: `offert.php` som endpoint, offert-bekräftelsetext,
      info@sodralasteknik.se i felmeddelanden. Filvalideringen (5 MB/fil, 15 MB totalt) behållen.
- [x] `privacy-policy/index.html` — integritetspolicy anpassad för Södra: personuppgiftsansvarig
      Södra Låsteknik AB (org.nr 559391-6835), offertformulär + bild-/ritningsbilagor, One.com som
      personuppgiftsbiträde (webbhotell/mejl), inga cookies/analys. Senast uppdaterad 2026-07-21.
- [x] `404.html`, `robots.txt`, `sitemap.xml`, `manifest.webmanifest` — skapade och anpassade till
      sodralasteknik.se och de faktiska sidorna (start, contact, privacy-policy). 404 använder Södras
      fotofria mörka hero. Manifest inkopplat i `<head>` på de tre huvudsidorna.
- [x] Lokal röktest: `python serve.py` + alla sidor svarar 200 (start, contact, privacy, 404,
      robots, sitemap, manifest).

## 5. Vad som ÅTERSTÅR

Ungefär i ordning:

- [x] **`contact/index.html`** — kontakt/offert-sida. Klar (se §4).
- [x] **`contact/offert.php`** — PHP-mottagare med bilagor. Klar (se §4). **Måste fortfarande
      testas skarpt på One.com** — verifiera att mejl + bilagor landar (§6).
- [x] **`assets/js/contact.js`** — justerad till offert-ordalydelse (se §4).
- [x] **`privacy-policy/index.html`** — klar (se §4).
- [x] **`404.html`** — klar (se §4).
- [x] **`robots.txt`, `sitemap.xml`, `manifest.webmanifest`** — klara (se §4).
- [ ] **Meta/OG-bild** — ev. en delningsbild (og-image). Nordö har en mall i `tools/`. OG-taggar
      finns redan i sidhuvudena men pekar inte på någon bild ännu (`og:image` saknas).
- [ ] **Nav/footer på alla sidor** — hålls i synk manuellt (duplicerad markup, som Nordö). Nu
      dubblerad på 3 sidor (start, contact, privacy) — ändringar måste in på alla.
- [ ] **Deploy till One.com** — sätt SFTP-secrets, bekräfta webbrot, testa. Verifiera SSL/HTTPS.
- [ ] **Skarpt mejltest av offertformuläret på One.com** — se §6. Kräver PHP-host (inte serve.py).
- [ ] **Cache-busting** — alla CSS/JS refereras med `?v=1.0.0`. Bumpa i alla HTML vid ändring.

## 6. One.com-specifika noteringar (viktigt)

- **Deploy sker via SFTP**, inte FTPS. `deploy.yml` är omskriven för det men behöver secrets
  (`SFTP_SERVER`, `SFTP_USERNAME`, `SFTP_PASSWORD`) och rätt `remote_path` (One.coms webbrot).
- **PHP finns på One.com**, men mejl via PHP:s `mail()` kan bete sig annorlunda än på Loopia.
  På Loopia krävdes två fixar för Nordös formulär: (1) envelope-avsändare via `-f`, (2) hela
  meddelandet 7-bitars rent (MIME-kodade headers + base64-brödtext) för att undvika SMTPUTF8-studs.
  Bär över samma härdade PHP och **testa mejlleveransen skarpt** — landar den inte, är
  reservlösningen autentiserad SMTP via PHPMailer mot One.coms SMTP-server.
- **Självuppdatering:** kunden vill gärna kunna lägga till bilder / uppdatera info själva "om det
  inte är för krångligt". Sajten är handredigerad HTML (inget CMS). Öppen fråga hur man löser det —
  enklast är att Daniel gör ändringarna, alternativt ett enkelt bild-/textblock de kan redigera.

## 7. Öppna frågor / väntar på kunden

- **Bilder** — hero (servicebilar), om-oss (låskista) och varför-oss (hänglås) är nu på plats
  (2026-07-22). Kvar: ev. fler bilder (team, arbete pågår) och en delningsbild (og-image).
- **Team-presentation** — vill de presentera Thomas, Markus, Jesper med namn/bild? Underlag delvis
  i formuläret (avsnitt 2), men bekräfta vad som får publiceras.
- **Google-omdömen/kundcitat** — inga angivna ännu; fråga om referenser som får citeras.
- **Öppettider** — bekräfta om 08–17 ska stå på sajten.
- **Självuppdatering** — se avsnitt 6.
- **Besöksstatistik** — "alltid bra men inget måste". Kräver analys + cookie-ruta om det läggs till.

## 8. Så här jobbar du lokalt

```bash
cd C:\git\sodralasteknik-website
python serve.py            # http://localhost:8000  (statiska filer)
# php -S localhost:8000    # när offert.php ska testas end-to-end (kräver PHP lokalt)
```

## 9. Loggbok

**2026-07-21.** Projektet startat. Underlag (formulär + 3 loggor) inkom från kunden. Beslut:
återanvänd Nordö-grunden med ny identitet, hosting på One.com. Byggde designsystem (`--sl-`,
grön/grå/svart, Space Grotesk + Inter), ombrandade main.css, tog fram favicon ur emblemet, och
byggde komplett startsida med fotofri hero. Skrev om deploy till One.com/SFTP. Repot gjort
självförsörjande och dokumenterat (denna fil + README + CLAUDE.md) så bygget kan fortsätta
härifrån utan Nordö-repot.

**2026-07-21 (forts.).** Byggde kontakt-/offertsidan (`contact/index.html`) med kontaktkort,
offertformulär (namn/företag/e-post/telefon/typ/beskrivning + bild-/ritningsbilagor) och FAQ,
samt den härdade PHP-mottagaren (`contact/offert.php`, kopierad och anpassad från Nordös
`serviceanmalan.php`). Ombrandade `contact.js` till offert-ordalydelse. Nav/footer i synk med
startsidan. PHP kunde inte lintas/testas lokalt (ingen PHP installerad). Nästa gång:
integritetspolicy + 404 + meta-filer (robots/sitemap/manifest), och därefter deploy till One.com
med skarpt mejltest av offertformuläret (§5, §6).

**2026-07-21 (forts. 2).** Byggde de återstående sidorna: `privacy-policy/index.html`
(anpassad efter Södra – One.com som biträde, offertformulär + bilagor, inga cookies), `404.html`
(Södras fotofria mörka hero), samt `robots.txt`, `sitemap.xml` och `manifest.webmanifest`. Kopplade
in manifestet i sidhuvudena. Röktestade lokalt med `serve.py` – alla sidor (start, contact, privacy,
404, robots, sitemap, manifest) svarar 200. Sajten är därmed innehållsmässigt klar lokalt.
Nästa gång: deploy till One.com (SFTP-secrets + webbrot), skarpt mejltest av offert.php, ev. og-image.

**2026-07-22.** Lade in kundens egna foton och pensionerade den fotofria designen. `hero.png`
(servicebilarna) blev mörk hero-bakgrund med overlay på alla sidor (start, kontakt, policy, 404),
`om-oss.png` fyller om-oss-boxen och `varfor-oss.png` varför-oss-boxen (citatet överlagrat på
bilden, ingen grön box). Bumpade cache-busting `?v=1.0.0` → `?v=1.1.2` i alla HTML. Röktestat lokalt –
alla sidor + bildresurser svarar 200. Uppdaterade CLAUDE.md och PROJEKT.md (fotobeslutet).
Pushat repo ligger på GitHub (Sodra-Lasteknik/sodralasteknik-website) sedan igår.
