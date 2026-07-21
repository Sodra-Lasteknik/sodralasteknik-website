# Södra Låsteknik – webbplats

Statisk webbplats för **Södra Låsteknik AB**, en låssmed i Stockholm (B2B: bygg, fastighet, BRF).
Vanilla HTML, CSS och JavaScript — inget byggsteg, inga beroenden. Innehåll på svenska.
Grön/grå/svart profil som matchar loggan. Domän **sodralasteknik.se**, driftas på **One.com**.

> **Bygget pågår.** Se **`PROJEKT.md`** för fullständig status — vad som är gjort, vad som
> återstår, bolagsfakta och One.com-specifika detaljer. Öppna det här repot för att fortsätta;
> allt som behövs finns här.

## Struktur

```
index.html                    Startsida (klar)
contact/                      Kontakt/offert-sida + offert.php   (att bygga)
privacy-policy/               Integritetspolicy                  (att bygga)
404.html                      Felsida                            (att bygga)
assets/css/tokens.css         Designtokens (--sl-, grön/grå/svart)
assets/css/main.css           All layout och styling
assets/js/main.js             Navigering, scroll-effekter, reveal
assets/js/contact.js          Validering + inskick av formulär (med bildbilagor)
assets/img/logo.png           Full logotyp (lockup)
assets/img/logo-text.png      Ordbild
assets/img/emblem.png         Sköld-emblem (även hero-vattenstämpel)
assets/img/favicon.png        Favicon (beskuret emblem)
serve.py                      Lokal utvecklingsserver
.github/workflows/deploy.yml  Deploy till One.com via SFTP (behöver secrets)
```

## Utveckla lokalt

```bash
python serve.py            # http://localhost:8000  (statiska filer)
# php -S localhost:8000    # för att testa offert.php end-to-end (kräver PHP lokalt)
```

## Deploy

Driftas på **One.com** (SFTP). Se `deploy.yml` och `PROJEKT.md` §6 för secrets och webbrot.
One.com stödjer PHP, vilket offertformuläret använder för att kunna ta emot bildbilagor.

## Designsystem

Byt utseende genom att redigera `assets/css/tokens.css` (variabler med `--sl-`-prefix), inte
`main.css`. Palett samplad ur loggan: grön `#387838`, grå `#808080`, nära-svart text.
