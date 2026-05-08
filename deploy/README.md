# Deploy hooks

Server-agent deploy steps for `hernextmission.org`.

## Flow

1. **GitHub webhook** fires on push to `main` (signed with the secret at `/root/.hnm-webhook-secret`).
2. **Server agent verifies the signature**, then `git pull`s `origin/main` into the repo checkout (e.g. `/var/www/hernextmission/site/`).
3. **Agent runs `deploy/post-deploy.sh`** with `WP_ROOT=/var/www/hernextmission` and `HNM_REPO_ROOT=/var/www/hernextmission/site`. The script:
   - rsyncs the theme and `hnm-*` plugins from the repo into the WP tree (only those subdirs — uploads and core are untouched);
   - activates the theme and plugins via WP-CLI;
   - flushes rewrites;
   - sets site identity;
   - seeds taxonomy terms and core pages.
4. **Site is live** at `https://hernextmission.org`.

`post-deploy.sh` is idempotent — safe to run on every deploy.

## One-shot manual deploy (use if the webhook isn't wired up yet)

```bash
cd /var/www/hernextmission/site
git pull origin main
WP_ROOT=/var/www/hernextmission \
HNM_REPO_ROOT=/var/www/hernextmission/site \
    bash deploy/post-deploy.sh
```

## Smoke check the deployed version matches origin/main

```bash
curl -s https://hernextmission.org/wp-content/themes/her-next-mission/style.css | grep '^Version:'
```

That `Version: X.Y.Z` value must match the `Version:` line in this commit's `wp-content/themes/her-next-mission/style.css`. If it doesn't, the rsync step didn't run (most likely cause: webhook not configured, so the agent never ran post-deploy.sh).

## What `post-deploy.sh` does

- Activates the `her-next-mission` block theme.
- Activates the `hnm-crm` and `hnm-sponsor-deck` plugins.
- Flushes WP rewrites (so custom post type and `/sponsor-deck-download/` URLs work).
- Sets site identity (`blogname`, `blogdescription`, `siteurl`, `home`).
- Sets pretty permalinks.
- Ensures these pages exist (creates them once, never overwrites): About, Services, Podcast, Events, Book a Call, Become a Sponsor, Sponsor Deck, Give, Featured Sponsors, Contact, Privacy.
- Sets a static front page (Home) if one isn't already configured.

## What `post-deploy.sh` does NOT do

- Does not install WordPress or run `wp core install` — agent does that out of band.
- Does not create users — admin user `jeff.cline@me.com` is created by the agent.
- Does not configure SMTP — handle in wp-admin or via a separate plugin.
- Does not upload the sponsor-deck PDF — Jeff/Krystalore upload via Media Library and paste the attachment ID into Settings → Sponsor Deck.

## Webhook example (for the agent)

```bash
# Pseudo-flow — adapt to whatever the agent supports.
SIGNATURE=$(cat /root/.hnm-webhook-secret)
# ... verify GitHub HMAC ...
cd /var/www/hernextmission
git -C wp-content/themes/her-next-mission pull origin main         # if theme is its own checkout
# OR: agent maintains a single checkout of the whole repo and rsyncs into wp-content/
git pull origin main
WP_ROOT=/var/www/hernextmission bash /path/to/repo/deploy/post-deploy.sh
```

## Environment

- WordPress 6.9.4
- PHP 8.3.6
- WP-CLI 2.12.0

## Smoke check after each deploy

```
curl -sI https://hernextmission.org | head -1                 # 200 OK
curl -s https://hernextmission.org | grep -q "Her Next"       # contains brand mark
curl -s https://hernextmission.org/sponsor-deck/ | grep -q "Sponsor Deck"
```
