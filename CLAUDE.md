# CLAUDE.md

Guidance for Claude Code when working in this repository.

## What this is

Marketing website for **Södra Låsteknik AB**, a locksmith in Stockholm working mainly B2B
(construction firms, property owners, housing associations). Static site: vanilla HTML, CSS,
JavaScript — **no build step, no npm, no framework**. Content is **Swedish** (UTF-8). Domain
**sodralasteknik.se** is hosted on **One.com** (not Loopia). Nothing is deployed yet; the site
is being built locally.

**Read `PROJEKT.md` first** — it is the living project doc: business facts, decisions, what's
done, what's left, and the One.com specifics. Keep it updated as work progresses.

## Origin

This repo reuses the proven foundation from the Nordö Förvaltning site (`C:\git\Website`):
same static/tokens approach, the same `main.js` / `contact.js`, and the same PHP contact-form
pattern. **This repo is self-contained** — do not depend on the Nordö repo to build here.

## Commands

- `python serve.py` — local preview at http://localhost:8000. Static only; does **not** run PHP,
  so the offert form won't submit under this server.
- `php -S localhost:8000` — use when testing the offert form end-to-end (runs `contact/offert.php`).

No test suite, linter, or build tooling.

## Architecture

**Pages.** `index.html` (one-page site with all sections). Planned/added over time:
`contact/index.html` (offert form + FAQ), `privacy-policy/index.html`, `404.html`. See `PROJEKT.md`
§5 for what's built vs pending. The **navbar** and **footer** markup are **duplicated** on each
page — changes must go to every page.

**Design system.** `assets/css/tokens.css` defines all colors, spacing, typography, radii and
shadows as CSS custom properties prefixed **`--sl-`**. `main.css` consumes them and should hold
no hard-coded palette values — **re-skin by editing the tokens**. Palette sampled from the logo:
green `#387838` (`--sl-brand`, the accent used for buttons, eyebrows, numerals, underlines),
grey `#808080`, near-black text, white / light-grey backgrounds, dark green-black for the hero
and panels. Fonts: Space Grotesk (headings) + Inter (body) from Google Fonts. Single theme, no
`prefers-color-scheme`.

**Token prefix note.** `main.css` was adapted from the Nordö site by renaming `--n-` → `--sl-`
(and the old gold accent tokens `--n-gold`/`--n-gold-deep` → `--sl-brand`/`--sl-brand-deep`).
If you copy more CSS/HTML from the Nordö repo, run the same rename or it won't pick up the tokens.

**Södra-specific CSS** lives in a clearly-marked section at the **end of `main.css`**
("Södra Låsteknik – komponenter utöver den delade grunden"): the `.hero-dark` photo hero, the
3-column `.services-grid-3`, the `.brand-grid` chips, and the `.why-panel` / `.about-panel`
photo boxes. **The site now uses the customer's own photos** (added 2026-07-22): `hero.png` (the
two branded service vans) is the dark hero background under a legibility overlay on every page
(`.hero-dark`, `.page-hero`, `.legal-hero`); `om-oss.png` fills `.about-panel` and `varfor-oss.png`
fills `.why-panel`. The old emblem watermarks (`.hero-emblem`, `.why-panel::before`) are retired —
`.hero-emblem` is now `display:none`. Dark overlays keep white hero text readable, so new hero
photos should stay reasonably dark or the overlay opacity needs raising. `.about-panel` and `.why-panel`
share one rule: each is a full-bleed photo with a short quote (blockquote + cite) overlaid on the
lower-left, made readable by a bottom-up dark gradient (`::after`) — no coloured box.

**JavaScript** (plain IIFEs): `assets/js/main.js` (navbar scroll state, mobile menu, reveal
animations, footer year, motion layer — all gated behind `prefers-reduced-motion`) loaded on every
page; `assets/js/contact.js` (client validation + form submit, incl. file-attachment size checks)
loaded on the contact page. JS is palette-independent.

**Offert form (to build).** `contact/index.html` will post to `contact/offert.php` — a PHP endpoint
on One.com that emails the request with **image/drawing attachments** to info@sodralasteknik.se.
Web3Forms' free plan can't take file uploads, hence PHP. **Reuse the hardened Nordö endpoint**
(`C:\git\Website\service\serviceanmalan.php`): it keeps nothing on disk, validates file type with
`finfo` against real bytes, has a honeypot, and — critically — carries two Loopia mail fixes
(envelope-from via `-f`, and a fully **7-bit-clean** message: MIME-encoded headers + base64 body).
**On One.com, re-verify mail delivery** — `mail()` behaviour differs per host; if it fails, fall
back to authenticated SMTP via PHPMailer against One.com's SMTP server.

**Logos & imagery.** `assets/img/logo.png` (full horizontal lockup, transparent), `logo-text.png`
(wordmark), `emblem.png` (grey shield — used as hero/panel watermark), `favicon.png` (tight-cropped
emblem). All transparent PNGs. The logo is a wide lockup, so `.brand-logo` height is tuned lower
than a compact symbol would be (see the Södra CSS section).

## Conventions

**Cache-busting.** CSS/JS are referenced with `?v=x.y.z` (currently `v=1.0.0`). When you change a
CSS or JS file, bump the version in **every** HTML file referencing it so browsers don't serve
stale files.

**Business facts** that must stay in sync across pages: footer (all pages), the JSON-LD
`Locksmith` block in `index.html` (org.nr 559391-6835, info@sodralasteknik.se, phone +468193767),
`sitemap.xml`, and meta/OG tags. The phone also appears as `tel:+468193767` links in the nav, hero,
CTA and footers.

## Deploy

Hosted on **One.com** via SFTP. `.github/workflows/deploy.yml` uploads on push to `main`, but needs
repo secrets `SFTP_SERVER` / `SFTP_USERNAME` / `SFTP_PASSWORD` and the correct `remote_path`
(One.com web root) — see the comments in that file and `PROJEKT.md` §6. Until that's set, upload
manually via an SFTP client (FileZilla → ssh.one.com). One.com supports PHP.
