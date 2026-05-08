#!/usr/bin/env bash
#
# Her Next Mission — post-deploy hook for the server agent.
#
# Run this AFTER the agent has pulled the latest commit into the WordPress
# wp-content/ tree on the server. It activates the theme and plugins,
# flushes rewrites, ensures core pages exist, and seeds taxonomy terms.
#
# Idempotent: safe to run on every deploy.
#
# Required env / args:
#   WP_ROOT  — absolute path to WordPress install root (where wp-config.php lives).
#              If unset, defaults to /var/www/hernextmission.
#
# Usage:
#   WP_ROOT=/var/www/hernextmission bash deploy/post-deploy.sh
#
# Exit codes:
#   0 — success
#   1 — wp-cli not found or WP_ROOT invalid
#   2 — theme/plugin activation failed

set -euo pipefail

WP_ROOT="${WP_ROOT:-/var/www/hernextmission}"

log() { printf '[hnm-deploy] %s\n' "$*"; }
fail() { printf '[hnm-deploy] ERROR: %s\n' "$*" >&2; exit 1; }

command -v wp >/dev/null 2>&1 || fail "wp-cli not found in PATH"
[ -d "$WP_ROOT" ] || fail "WP_ROOT does not exist: $WP_ROOT"
[ -f "$WP_ROOT/wp-config.php" ] || fail "wp-config.php not found in $WP_ROOT"

cd "$WP_ROOT"

WP="wp --path=$WP_ROOT --allow-root"

log "Activating theme: her-next-mission"
$WP theme activate her-next-mission || { echo "theme activation failed"; exit 2; }

log "Activating plugin: hnm-crm"
$WP plugin activate hnm-crm || { echo "hnm-crm activation failed"; exit 2; }

log "Activating plugin: hnm-sponsor-deck"
$WP plugin activate hnm-sponsor-deck || { echo "hnm-sponsor-deck activation failed"; exit 2; }

log "Flushing rewrites"
$WP rewrite flush --hard

log "Setting site identity"
$WP option update blogname    "Her Next Mission Foundation"
$WP option update blogdescription "It's her turn. For female veterans and first responders transitioning out of service."
$WP option update siteurl     "https://hernextmission.org"
$WP option update home        "https://hernextmission.org"
$WP option update timezone_string "America/New_York" --quiet || true

log "Disabling default WP comments on new posts (foundation site, not a blog)"
$WP option update default_comment_status "closed"
$WP option update default_ping_status "closed"

# Pretty permalinks
log "Setting permalink structure"
$WP rewrite structure '/%postname%/'

# Ensure core pages exist (idempotent)
ensure_page() {
    local title="$1"
    local slug="$2"
    local content="${3:-}"
    if ! $WP post list --post_type=page --name="$slug" --field=ID | grep -q .; then
        log "Creating page: $title (/$slug/)"
        $WP post create --post_type=page --post_status=publish --post_name="$slug" --post_title="$title" --post_content="$content"
    else
        log "Page exists: /$slug/"
    fi
}

ensure_page "About"             "about"             "<p>About Her Next Mission Foundation.</p>"
ensure_page "Services"          "services"          "<p>Programs and services.</p>"
ensure_page "Podcast"           "podcast"           "<p>The Her Next Mission podcast.</p>"
ensure_page "Events"            "events"            "<p>Summit, retreats, and events.</p>"
ensure_page "Book a Call"       "book-a-call"       "<p>Book a discovery call.</p>"
ensure_page "Become a Sponsor"  "sponsors"          "<p>Featured sponsors of Her Next Mission.</p>"
ensure_page "Sponsor Deck"      "sponsor-deck"      "[hnm_sponsor_deck_form]"
ensure_page "Give"              "give"              "<p>Donate to Her Next Mission.</p>"
ensure_page "Featured Sponsors" "featured-sponsors" "<p>Our featured sponsors.</p>"
ensure_page "Contact"           "contact"           "<p>Get in touch.</p>"
ensure_page "Privacy"           "privacy"           "<p>Privacy policy.</p>"

# Make the homepage a static front page if a front-page hasn't been set.
if [ "$($WP option get show_on_front)" != "page" ]; then
    log "Setting static front page (front-page.html template applies automatically for blocks themes)"
    # Create a Home page if one doesn't exist
    if ! $WP post list --post_type=page --name="home" --field=ID | grep -q .; then
        $WP post create --post_type=page --post_status=publish --post_name="home" --post_title="Home" --post_content=""
    fi
    HOME_ID=$($WP post list --post_type=page --name="home" --field=ID | head -n1)
    $WP option update show_on_front "page"
    $WP option update page_on_front "$HOME_ID"
fi

log "Done."
