# Changelog

All notable changes to the WebCodes website project.

---

## [2.2.0] - 2026-04-11

### English Language Support

Added full English translation with a client-side language toggle.

### Added

- **Language toggle button** in header navbar — pill-shaped "EN"/"ET" button to switch between Estonian and English
- **`js/i18n.js`** — complete i18n system with `data-i18n` attribute-based text replacement, `data-i18n-placeholder` for form inputs, and localStorage persistence
- **`.btn-lang` CSS** — styled toggle button matching the existing design system (pill shape, primary color border, hover fill)
- **Full English translations** — all page content: hero, benefits, process steps, portfolio, FAQ (5 items), CTA section, form labels, placeholders, and notification messages

### Changed

- **`index.html`** — added `data-i18n` and `data-i18n-placeholder` attributes to all translatable text elements across the page
- **`js/script.js`** — form notification messages now use current language from translations object instead of hardcoded Estonian
- **`<html lang>` attribute** — dynamically updates to `et` or `en` based on selected language (improves accessibility and SEO)
- **Phone label structure** — wrapped in spans to support independent translation of "Telefon" and "(valikuline)"

---

## [2.1.0] - 2026-04-11

### Broader Service Positioning & SEO Improvements

Updated messaging to reflect that WebCodes offers IT systems and solutions alongside websites, and added SEO enhancements.

### Changed

- **Page title & meta description** — broadened from "kodulehed väikefirmadele" to "kodulehed ja IT-lahendused ettevõtetele"
- **H1 headline** — now includes "ja IT-lahendused sinu ettevõttele"
- **Hero subheading** — mentions websites and IT solutions
- **Trust badges** — changed to "Iga klient loeb" and "Sinu idee, meie teostus" (removed "50+ klienti" and "Eesti väikefirmadele")
- **Benefits heading** — "Miks ettevõtted valivad meid" (was "väikefirmad")
- **Fast delivery benefit** — added "IT-lahendused kokkuleppel"
- **How it works steps 3 & 4** — broadened to cover IT solutions, step 4 renamed "Projekt valmis"
- **FAQ pricing** — updated price to 300€, added IT solutions pricing info
- **FAQ technology** — "Kodulehe jaoks soovitame WordPressi... keerulisemate kodulehtede ja IT-süsteemide jaoks ehitame eritellimusel"
- **FAQ maintenance** — minor text fix
- **Portfolio** — replaced Paide lasteaed with Agilityliit competition calendar system
- **Testimonials section** — commented out (to be re-added with real testimonials)
- **Header** — removed phone number from header
- **Footer** — removed phone number
- **CTA section** — simplified subheading

### Added

- **New FAQ item** — "Milliseid IT-lahendusi te pakute?" covering internal systems, automation, databases, APIs
- **Open Graph tags** — og:title, og:description, og:type, og:url, og:locale, og:site_name, og:image
- **Canonical URL** — `<link rel="canonical" href="https://webcodes.ee/" />`
- **Robots meta tag** — `<meta name="robots" content="index, follow" />`
- **Theme color** — `<meta name="theme-color" content="#FF8A5B" />` for mobile browsers
- **JSON-LD structured data** — ProfessionalService schema with aggregate rating
- **JSON-LD FAQ schema** — FAQPage structured data for Google rich results

---

## [2.0.0] - 2026-04-09

### Complete Redesign — Warm & Friendly Lead Generation Landing Page

Full redesign from a minimalist service page to a warm, pastel-toned, mobile-first lead generation site targeting Estonian small business owners and startups.

### Added

- **Warm pastel design system** — new CSS custom properties: cream background (`#FFF9F3`), coral primary (`#FF8A5B`), pastel mint/lavender/yellow accents, rounded corners (20–32px), pill-shaped buttons
- **Quicksand font** for headings (loaded via Google Fonts alongside Rubik)
- **Lead generation form** in hero section — fields: Nimi, E-post, Telefon (optional), Mis projekt? + "Saada päring" CTA
- **Second lead form** in final CTA section ("Räägime sinu projektist")
- **Honeypot spam protection** — hidden `website` field in both forms; bots that fill it get silently rejected
- **Trust badges row** in hero — "4.9/5", "50+ rahulolevat klienti", "Eesti väikefirmadele"
- **"Miks väikefirmad valivad meid"** — 3 pastel benefit cards (mint, lavender, yellow) with existing SVG icons
- **"Kuidas see töötab"** — 4-step process section (Võta ühendust → Tasuta konsultatsioon → Disain & arendus → Koduleht valmis)
- **Testimonials section** — 2 warm quotes (Mari Tamm / Kohvik Kaneel, Jaan Kask / Kask Ehitus OÜ)
- **FAQ accordion** — 4 questions using native `<details>`/`<summary>` (no JS needed): hind, ajakulu, WordPress vs custom, hooldus
- **Per-form notification system** — success (green) and error (red) messages scoped to each form
- **Visible error messages** — JS now shows Estonian error messages from PHP to the user instead of silent `console.error`
- **Sticky header** with phone number + "Küsi tasuta pakkumist" CTA button
- **Decorative hero blobs** — pastel lavender and mint blurred circles for visual warmth
- **Eyebrow pill** component ("Tere tulemast")
- **`.lead-form--compact`** CSS modifier for the bottom CTA form
- **`scroll-margin-top: 9rem`** on sections to offset sticky header on anchor navigation
- **Footer note** — "Tehtud armastusega Eestis"

### Changed

- **index.html** — full rewrite from service page to lead-gen landing page (Estonian)
- **general.css** — rewritten with warm pastel design tokens, new typography scale (clamp-based), pill buttons with hover lift, notification success/error states
- **styles.css** — rewritten with new section styles (hero, benefits, process, work, testimonials, FAQ, CTA, footer), sticky translucent header, form card styling
- **queries.css** — simplified from multiple breakpoints to single mobile-first 768px breakpoint
- **js/script.js** — rewritten: `.lead-form` selector, per-form notification scoping via `form.querySelector('.notification')`, `fetch(form.action)` instead of hardcoded path, visible error messages with Estonian copy
- **php/send_email.php** — rewritten for lead-gen: accepts full-name, email, phone, message fields; hardened with input validation, `clean_line()` and `clean_body()` sanitizers, `FILTER_VALIDATE_EMAIL`, length limits, header-injection protection, file-based rate limiting (30s per IP with `LOCK_EX`), honeypot check, safe `From`/`Reply-To` headers, MIME-encoded subject, Estonian success/error messages
- **Logo reference** updated to `Kaasaegne_teaduslogo_ja_branding.png`
- **Meta description** and `<title>` updated for lead-gen focus
- **Color palette** changed from blue (`#1c7ed6`) to warm coral/cream pastels
- **Section padding** increased: 80px mobile, 140px desktop
- **Button style** changed from flat to pill-shaped with shadow + lift animation

### Fixed

- **Honeypot actually works** — `website` input added to both HTML forms (previously PHP checked it but HTML never sent it)
- **Per-form feedback** — each form has its own `.notification` div (previously only hero had one, bottom form showed no feedback)
- **Rate limit after validation** — invalid submissions no longer trigger the 30s IP lockout
- **Header injection** — user email moved to `Reply-To`, safe hardcoded `From: WebCodes <info@webcodes.ee>`
- **`clean_line()`** — removed dead `%0a`/`%0d` literal string stripping (URL-encoded values aren't present in `$_POST`)
- **Rate limit file locking** — added `LOCK_EX` to prevent race conditions under concurrent requests
- **Honeypot `aria-hidden="true"` removed** — invalid on form controls; `tabindex="-1"` + `.visually-hidden` is sufficient
- **Label/textarea ID mismatch** — old `for="sõnum"` vs `id="message"` bug eliminated (new form structure)
- **`.heading.secondary` typo** in old queries.css — dot instead of dash; eliminated in rewrite

### Removed

- **Pricing section** (Tunnitöö €25 / Põhiplaan €500) — removed from landing page
- **"Why choose us" section** — replaced with benefit cards
- **Old dark blue theme** (`#1c7ed6`) — fully replaced
- **Glow animations** on why-section images
- **Mobile hamburger nav** — replaced with simple header CTA
- **Newsletter-only flow** — replaced with full lead-gen form
- **Old hero image** (`placeholder.webp`) — hero is now text + form card
- **ion-icons** dependency — removed (was used for close icon in pricing)
- **Multiple CSS breakpoints** (1344px, 1200px, 944px, 704px, 560px) — consolidated to single 768px

### Security

- POST-only enforcement (405 on other methods)
- Email validation via `filter_var(FILTER_VALIDATE_EMAIL)`
- CR/LF/NUL stripping on all single-line inputs
- Length limits: email ≤254, name ≤100, phone ≤30, message ≤5000
- Honeypot bot trap (silent success on filled `website` field)
- IP-based rate limiting (30s cooldown, file-locked)
- Safe mail headers (no user input in `From`)
- MIME-encoded UTF-8 subject line
- JSON responses with proper `Content-Type` header

---

## [1.0.0] - Initial

- Original WebCodes service website
- Blue theme, Rubik font
- Hero with placeholder image
- "Why choose us" 3-step section
- Portfolio (Paide lasteaed)
- Pricing table (hourly + basic plan)
- Contact form
- Sticky nav with mobile hamburger menu
- Multiple responsive breakpoints
