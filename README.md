# Her Next Mission Foundation — WordPress

Custom WordPress theme + plugins for [hernextmission.org](https://hernextmission.org).

> Empowering female veterans and first responders transitioning out of service to reclaim their identity, rebuild their confidence, and discover their next mission.

## Repo layout

```
wp-content/
  themes/her-next-mission/      # block theme (FSE)
  plugins/hnm-crm/              # lead CPT + admin (sponsor deck + contact leads)
  plugins/hnm-sponsor-deck/     # gated sponsor-deck request form
deploy/                          # server-agent deploy hooks (WP-CLI)
docs/superpowers/specs/          # design doc
```

This repo holds **custom code only**. WP core, default plugins, uploads, and the database live on the server and are managed by the server agent.

## Stack

- WordPress 6.9.4
- PHP 8.3.6
- WP-CLI 2.12.0

## Deploy

Server agent listens on a webhook (secret at `/root/.hnm-webhook-secret`). On push to `main`:

1. Agent pulls latest from `origin/main` into the server's `wp-content/` tree.
2. Agent runs `deploy/post-deploy.sh` which calls WP-CLI to:
   - activate the theme `her-next-mission`
   - activate `hnm-crm` and `hnm-sponsor-deck` plugins
   - flush rewrites
   - ensure foundational taxonomy terms exist (Wellbeing, Transition, Understanding, Clarity, Identity)
3. Site is live at `https://hernextmission.org`.

## Brand assets — placeholder status

The first push uses placeholders. Replace with real assets as they arrive:

- **Logo** — `wp-content/themes/her-next-mission/assets/images/logo.svg` (currently a typographic wordmark). Drop a real SVG with the same filename or upload via Customizer.
- **Color palette** — defined in `wp-content/themes/her-next-mission/theme.json` and `assets/css/main.css` as CSS custom properties (`--hnm-navy`, `--hnm-cream`, `--hnm-gold`, `--hnm-sage`, `--hnm-paper`, `--hnm-ink`). Swap hex values in those two places.
- **Photography** — hero, True North compass, in-service photos, podcast cover. Upload via wp-admin → Media, then assign in front-page sections.

## Admins

- `jeff.cline@me.com` (already created server-side; password rotated by Jeff on first login)
- Krystalore — to be added by Jeff in wp-admin → Users (admin role)

CRM lead access is restricted to users with the `manage_options` capability (admins only).

## License

Theme and plugins are licensed GPL-2.0-or-later, consistent with WordPress.
