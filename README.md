# Her Next Mission

Static landing site for **HER NEXT MISSION** — a 501(c)(3) nonprofit
(in formation) serving female Veterans and first responders in the
transition out of service.

## Stack

Plain HTML / CSS / JS. No build step, no framework, no PHP, no DB.

```
/                       → repo root
  index.html            → home
  about.html            → about + founder + true north
  programs.html         → programs / coaching / retreats
  podcast.html          → from-service-to-success podcast
  events.html           → summit + retreats + bootcamps
  sponsors.html         → 3 sponsor tiers + activations
  give.html             → impact tiers + ways to give
  book-a-call.html      → discovery-call intake
  contact.html          → audience-routed mailto cards
  privacy.html          → privacy policy
  /assets/
    /css/site.css       → single comprehensive stylesheet
    /js/site.js         → rocket animation + mobile nav + mailto helper
    /images/            → photos, logos, icons
      logo.png          → transparent phoenix mark
      compass-true-north.png → transparent brass-compass photo
      compass-melissa.jpg    → engraved version of the compass
      hero-krystalore.jpg    → founder mirror photo
      service-*.jpg          → founder's service photos
      /uniform/         → 20 royalty-free women-in-uniform photos (Pexels)
      /civilian/        → 5 royalty-free business-attire photos (Pexels)
      favicon.svg
      compass-bullet.svg
  /deploy/
    deploy.sh           → rsync to web root
    README.md
  README.md
  LICENSE
```

## Local preview

Any static server works. From the repo root:

```bash
python3 -m http.server 8080
# then open http://localhost:8080/
```

## Deploy

See `deploy/README.md`.

## CTAs

Every call-to-action button across the site routes to a `mailto:`
opening in the visitor's email client. Subjects follow the pattern
`<Button Title> - HER NEXT MISSION`. Bodies are pre-filled with
intake questions tailored to each intent (beneficiary, sponsor,
donor, podcast guest, etc.) plus a contact-info block.

The full catalog lives in `assets/js/site.js`. Adding a new CTA:

1. Add a new entry in the `CATALOG` object.
2. Add `data-cta="your-slug"` to a button.

`site.js` rewrites the `href` on page load.

## Imagery

All stock photos under `/assets/images/uniform/` and
`/assets/images/civilian/` are sourced from
[Pexels](https://www.pexels.com) under the
[Pexels License](https://www.pexels.com/license/) (free for
commercial use, modification permitted, attribution not required).
Per-photo credits are in `assets/images/uniform/CREDITS.md`.

## Why static

The previous WordPress build is in git history (any commit prior to
`v1.0`). Static is faster to host, easier to edit by hand, and
removes the WP attack surface. If a CMS is ever needed, the on-disk
structure is friendly to any SSG migration (Eleventy, Astro, Next.js).
