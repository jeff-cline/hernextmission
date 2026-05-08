---
date: 2026-05-08
title: Her Next Mission — WordPress design (v0.1)
status: in-progress (placeholder assets; iterating once real assets land)
---

# Her Next Mission Foundation — WordPress design

## What this is

Custom WordPress site for the Her Next Mission Foundation, serving female veterans and first responders transitioning out of service. Three audiences: women in transition (beneficiaries), corporate sponsors, individual donors. Built so Jeff and Krystalore can edit pages and posts from /wp-admin without touching code; built so the visual identity is swappable from a single CSS-variables file as soon as real brand assets arrive.

## Decisions locked

| Decision | Value | Why |
|---|---|---|
| Hosting | WordPress on user's server, managed by their agent | User chose; agent handles install + deploy via webhook |
| Stack | PHP 8.3.6, WP 6.9.4, WP-CLI 2.12.0 | Confirmed by user |
| Repo scope | Custom code only (theme + plugins + deploy/) | Cleanest, no merge pain on WP core updates |
| Domain | `hernextmission.org` (canonical), `www.hernextmission.org` redirect | User confirmed `.org`; agent reissuing certs |
| Theme architecture | Block theme (FSE) using theme.json + block patterns | Modern, future-proof, lets non-technical editors compose layouts |
| Brand assets in v0.1 | Placeholder logo, palette, photos | Real assets not yet provided; everything is one file/upload to swap |
| Credentials | Server-side only (`/root/hernextmission-secrets.txt`) | Never in repo, never in chat |

## Repo layout

```
hernextmission/
├── README.md
├── LICENSE                          # GPL-2.0-or-later
├── .gitignore
├── deploy/
│   ├── post-deploy.sh               # idempotent WP-CLI hook for the agent
│   └── README.md
├── docs/superpowers/specs/
│   └── 2026-05-08-her-next-mission-wp-design.md
└── wp-content/
    ├── themes/her-next-mission/
    │   ├── style.css                # theme metadata
    │   ├── theme.json               # palette, fonts, sizes, layout
    │   ├── functions.php            # bootstraps inc/*.php
    │   ├── index.php                # WP fallback
    │   ├── inc/
    │   │   ├── setup.php            # supports, scripts, styles
    │   │   ├── menus.php            # nav menu locations
    │   │   ├── cpt.php              # podcast_episode, story, event, sponsor + hnm_theme tax
    │   │   └── patterns.php         # registers HNM pattern category
    │   ├── parts/
    │   │   ├── header.html          # sticky header w/ wordmark + primary nav
    │   │   └── footer.html          # 4-col footer w/ mantra band, category nav, give/sponsor nav, meta nav
    │   ├── templates/
    │   │   ├── index.html           # blog listing
    │   │   ├── front-page.html      # composes 8 patterns
    │   │   ├── page.html            # generic page
    │   │   ├── single.html          # single post
    │   │   └── archive.html         # category/tag/term archive
    │   ├── patterns/                # 8 patterns referenced from front-page.html
    │   │   ├── hero.php             # rocket-launch logo + 3 audience CTAs + photo
    │   │   ├── mantra-band.php      # PAY IT FORWARD · HER TURN · IT'S HER TURN strip
    │   │   ├── cta-cards.php        # 3 cards: Beneficiaries, Sponsors, Donors
    │   │   ├── true-north.php       # compass feature ("a compass for what comes next")
    │   │   ├── services-grid.php    # 6-up: 1-on-1, bootcamps, retreats, summit, podcast, grants
    │   │   ├── mission-vision-values.php
    │   │   ├── podcast-feature.php  # cover + listen / submit story CTAs
    │   │   └── sponsor-deck-cta.php # final CTA → /sponsor-deck/
    │   └── assets/
    │       ├── css/main.css         # CSS custom properties + rocket animation + polish
    │       ├── js/rocket-launch.js  # one-shot launch animation on DOM ready
    │       └── images/              # placeholder SVGs (logo, hero, compass, podcast)
    └── plugins/
        ├── hnm-crm/                 # Lead CPT, admin columns, CSV export
        │   ├── hnm-crm.php
        │   └── includes/
        │       ├── class-lead-cpt.php
        │       ├── class-lead-repository.php   # only place that creates leads
        │       ├── class-admin.php
        │       └── class-csv-export.php
        └── hnm-sponsor-deck/        # gated PDF download
            ├── hnm-sponsor-deck.php
            └── includes/
                ├── class-form.php           # [hnm_sponsor_deck_form] shortcode
                ├── class-handler.php        # validate, capture lead, email admins
                ├── class-download.php       # one-time signed token, /sponsor-deck-download/
                └── class-settings.php       # Settings → Sponsor Deck (PDF id + admin emails)
```

## Visual system

### Color palette (placeholder)

Defined as both `theme.json` presets (so block UI knows about them) and CSS custom properties in `main.css` (so layouts can reference them directly).

| Slug | Hex | Role |
|---|---|---|
| `navy` | `#0A2540` | Primary brand, hero bg, headlines |
| `navy-deep` | `#061A2E` | Footer, deep accents |
| `cream` | `#F5F0E8` | Card backgrounds, warm contrast |
| `gold` | `#C9A961` | Accent, CTAs, eyebrow text |
| `gold-soft` | `#E5D4A1` | Hover, subtle accents |
| `sage` | `#6B7C5E` | Secondary accent |
| `paper` | `#FAFAF6` | Page background |
| `ink` | `#1A1A1A` | Body text |
| `ink-soft` | `#4A4A4A` | Secondary text |

To swap: change hex values in two places (`theme.json` `settings.color.palette` + `main.css` `:root`). Done.

### Typography

- **Display:** Cormorant Garamond (serif). Headlines, mantras, mission/vision/values pulls. Loaded from Google Fonts.
- **Body:** Inter (sans). Everything else. Same source.

### Hero rocket-launch animation

- Logo SVG starts off-screen (~`100vh + 200px` below) at `0.78` scale, opacity 0.
- Translates up with a slight overshoot (`-2vh`, `1.03`) and lands at `0`/`1` in 1.6s.
- Trail element fades up behind it then collapses.
- Triggers once on DOM ready, never replays.
- Honors `prefers-reduced-motion` → animation is suppressed; logo simply appears in place.

## Content model

### Pages (created idempotently by `post-deploy.sh`)

Home, About, Services, Podcast, Events, Book a Call, Become a Sponsor, Sponsor Deck (carries the form shortcode), Give, Featured Sponsors, Contact, Privacy.

### Custom post types

| Slug | Purpose | Public archive |
|---|---|---|
| `podcast_episode` | Podcast episodes (Apple/Spotify embed in custom field) | `/podcast/` |
| `story` | Submitted/featured stories | `/stories/` |
| `event` | Summit, retreats, bootcamps | `/events/` |
| `sponsor` | Featured sponsors w/ logos | `/sponsors/` |
| `hnm_lead` (in hnm-crm plugin) | Leads — admin-only, never public | n/a |

### Taxonomies

| Slug | Applies to | Terms (seeded on activate) |
|---|---|---|
| `hnm_theme` | post, story | Wellbeing, Transition, Understanding, Clarity, Identity |
| `sponsor_tier` | sponsor | Mission Partner ($250k), Lead Sponsor ($50k), Featured Sponsor ($25k), Supporting Sponsor |

### Nav menu locations

- `primary` — header (Podcasts, Book a Call, Events, About, …)
- `footer-categories` — footer left col (the foundational themes)
- `footer-give` — footer middle (Donate, Become a Sponsor, Featured Sponsors)
- `footer-meta` — footer right (Contact, Privacy, …)

Editors fill these via Appearance → Menus.

## CRM (hnm-crm)

### Goals

- Capture every lead (sponsor deck request first; more sources later) into a single Lead post type.
- Admin-only (`manage_options`). Not publicly queryable, not in REST.
- One canonical entry point: `Lead_Repository::insert()`. Other plugins call this; nothing else creates leads.
- CSV export at any time.

### Fields

| Meta key | Field |
|---|---|
| `_hnm_name` | Name |
| `_hnm_email` | Email |
| `_hnm_phone` | Phone |
| `_hnm_business` | Company / Org |
| `_hnm_source` | Where the lead came from (`sponsor-deck`, `contact`, etc.) |
| `_hnm_notes` | Free text (also stored as post content) |

Admin columns surface email, phone, and source on the list view. A meta box on the edit screen prints the structured data.

## Sponsor deck flow (hnm-sponsor-deck)

1. Page `/sponsor-deck/` contains the `[hnm_sponsor_deck_form]` shortcode.
2. Visitor submits → POST to `admin-post.php?action=hnm_deck_request`.
3. `Handler::handle()` verifies nonce, checks honeypot, validates name + email.
4. On success:
    - Creates a `hnm_lead` via `Lead_Repository::insert()` with `source = sponsor-deck`.
    - Emails admins (`jeff.cline@me.com` + site admin email by default; configurable in Settings → Sponsor Deck).
    - Issues a one-time signed token (sha1 of random + auth salt) stored in a 72h transient bound to the lead ID + email.
    - Emails the requester with `https://hernextmission.org/sponsor-deck-download/?hnm_deck_token=…`
    - Redirects the requester back to `/sponsor-deck/?hnm_deck=sent` (success state in the form template).
5. When the requester clicks the link, `Download::maybe_serve()` validates, deletes the transient (single-use), and streams the configured PDF.

### Configuration

Settings → Sponsor Deck:
- **PDF Attachment ID** — admin uploads the deck PDF to Media Library and pastes the attachment ID here.
- **Admin notification emails** — comma-separated list, defaults to site admin + `jeff.cline@me.com`.

### Failure modes

| Condition | Behavior |
|---|---|
| Honeypot filled | Silently redirect to success (don't leak that we detected a bot) |
| Missing/invalid email | Redirect with `hnm_deck=error` (form template can display message — TODO) |
| `hnm-crm` not active | `wp_die` with admin instruction |
| PDF not configured | `wp_die` 500 with admin instruction |
| Token expired/invalid | `wp_die` 410 with "request the deck again" |

## Deploy

`deploy/post-deploy.sh` runs on every successful pull. See `deploy/README.md`.

## What's a placeholder, not real

| Asset | Status | Action |
|---|---|---|
| Logo | SVG wordmark with rocket glyph | Drop real `assets/images/logo.svg` |
| Hero photo | Decorative SVG with "[hero portrait]" text | Upload to Media → set in `patterns/hero.php` (or wp-admin once we move the hero into a Page block) |
| True North compass | SVG illustration of a brass compass | Upload to Media → set in `patterns/true-north.php` |
| Podcast cover | SVG with mic glyph | Upload to Media → set in `patterns/podcast-feature.php` |
| Color palette | Vet-foundation classics (navy/cream/gold/sage) | When real palette arrives, swap hex in `theme.json` + `main.css` `:root` |
| Sponsor deck PDF | None bundled | Upload PDF to Media → paste ID in Settings → Sponsor Deck |
| Krystalore bio | None — couldn't research without input | Add to About page once provided |
| Real podcast episodes | None — empty CPT | Add via wp-admin → Podcast Episodes |

## What's intentionally NOT in v0.1

- Custom blocks (none needed yet — block patterns cover it).
- Newsletter signup.
- Donation processing (linked to `/give/` page, real Stripe/Donorbox/Givebutter integration is a follow-up).
- Stories submission form (separate plugin, similar pattern to sponsor deck).
- Search.
- Schema.org / structured data (low ROI until real content is in).
- Multilingual.
- Comments — disabled site-wide via `post-deploy.sh`.

## Open questions

- Krystalore — full name, role, bio. Needed for About page and the sponsor deck.
- Real images — when available, swap in.
- Real color palette — same.
- Sponsor deck PDF source — Jeff to produce or have produced; plugin handles delivery once uploaded.
- Donation provider (Stripe vs. Donorbox vs. Givebutter) — pick one and we'll wire it.
- Podcast feed source (RSS) — for the podcast page episode list, do we mirror an external RSS or maintain episodes inside WP only?

## Out-of-band work for the user

1. Tell the agent to redo Certbot for `hernextmission.org` + `www.hernextmission.org` (cancel `.com`).
2. Provide images and Krystalore details when ready.
3. After first deploy: log into wp-admin, build out the four nav menus (Appearance → Menus), upload the sponsor deck PDF to Media Library, and paste its attachment ID into Settings → Sponsor Deck.
