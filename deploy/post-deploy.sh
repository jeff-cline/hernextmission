#!/usr/bin/env bash
#
# Her Next Mission — post-deploy hook for the server agent.
#
# This script runs the full deploy: it syncs custom code from the repo
# checkout into the WordPress tree (if the two live in different places),
# activates theme + plugins, flushes rewrites, sets site identity, seeds
# taxonomy terms, and seeds page content.
#
# Idempotent: safe to run on every deploy.
#
#   - Pages that don't exist are created.
#   - Pages whose content matches a known placeholder are re-seeded with
#     the latest rich content.
#   - Pages with custom (admin-edited) content are left alone.
#
# Required env:
#   WP_ROOT        — absolute path to WordPress install root (where wp-config.php
#                    lives). Defaults to /var/www/hernextmission.
#   HNM_REPO_ROOT  — absolute path to the cloned repo on the server. Defaults
#                    to the directory containing this script's parent (i.e.
#                    "$(dirname this_script)/..", which works when the agent
#                    invokes it via deploy/post-deploy.sh from the repo root).
#
# What the rsync does:
#   If HNM_REPO_ROOT/wp-content != WP_ROOT/wp-content, this script rsyncs
#   the THEME and HNM PLUGINS from the repo to the WP tree. It does NOT
#   touch wp-content/uploads/, mu-plugins/, default WP themes/plugins, or
#   anything else. So user-uploaded media is safe.
#
# Usage:
#   WP_ROOT=/var/www/hernextmission \
#   HNM_REPO_ROOT=/var/www/hernextmission/site \
#       bash deploy/post-deploy.sh
#
# Exit codes:
#   0 — success
#   1 — wp-cli not found, WP_ROOT invalid, or rsync missing
#   2 — theme/plugin activation failed

set -euo pipefail

WP_ROOT="${WP_ROOT:-/var/www/hernextmission}"

# Resolve where this script lives, then derive the repo root (its parent's parent).
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HNM_REPO_ROOT="${HNM_REPO_ROOT:-$(cd "$SCRIPT_DIR/.." && pwd)}"

log()  { printf '[hnm-deploy] %s\n' "$*"; }
fail() { printf '[hnm-deploy] ERROR: %s\n' "$*" >&2; exit 1; }

command -v wp    >/dev/null 2>&1 || fail "wp-cli not found in PATH"
command -v rsync >/dev/null 2>&1 || fail "rsync not found in PATH"
[ -d "$WP_ROOT" ] || fail "WP_ROOT does not exist: $WP_ROOT"
[ -f "$WP_ROOT/wp-config.php" ] || fail "wp-config.php not found in $WP_ROOT"
[ -d "$HNM_REPO_ROOT/wp-content" ] || fail "HNM_REPO_ROOT/wp-content not found at $HNM_REPO_ROOT/wp-content"

log "WP_ROOT       = $WP_ROOT"
log "HNM_REPO_ROOT = $HNM_REPO_ROOT"

# ---------- sync custom code from repo to WP tree ----------

if [ "$HNM_REPO_ROOT" = "$WP_ROOT" ]; then
    log "Repo and WP root are the same — skipping rsync (files already in place)"
else
    sync_dir() {
        local src="$1"
        local dst="$2"
        if [ -d "$src" ]; then
            log "Syncing $src -> $dst"
            mkdir -p "$dst"
            rsync -a --delete "$src/" "$dst/"
        else
            log "Skipping (not in repo): $src"
        fi
    }

    sync_dir "$HNM_REPO_ROOT/wp-content/themes/her-next-mission"   "$WP_ROOT/wp-content/themes/her-next-mission"
    sync_dir "$HNM_REPO_ROOT/wp-content/plugins/hnm-crm"           "$WP_ROOT/wp-content/plugins/hnm-crm"
    sync_dir "$HNM_REPO_ROOT/wp-content/plugins/hnm-sponsor-deck"  "$WP_ROOT/wp-content/plugins/hnm-sponsor-deck"
fi

cd "$WP_ROOT"

WP="wp --path=$WP_ROOT --allow-root"

# ---------- theme + plugins ----------

log "Activating theme: her-next-mission"
$WP theme activate her-next-mission || { echo "theme activation failed"; exit 2; }

log "Activating plugin: hnm-crm"
$WP plugin activate hnm-crm || { echo "hnm-crm activation failed"; exit 2; }

log "Activating plugin: hnm-sponsor-deck"
$WP plugin activate hnm-sponsor-deck || { echo "hnm-sponsor-deck activation failed"; exit 2; }

log "Flushing rewrites"
$WP rewrite flush --hard

# ---------- site identity ----------

log "Setting site identity"
$WP option update blogname        "Her Next Mission"
$WP option update blogdescription "It's her turn. Coaching, community, and clarity for female veterans and first responders transitioning out of service."
$WP option update siteurl         "https://hernextmission.org"
$WP option update home            "https://hernextmission.org"
$WP option update timezone_string "America/New_York" --quiet || true

log "Disabling default WP comments (org site, not a blog)"
$WP option update default_comment_status "closed"
$WP option update default_ping_status    "closed"

log "Setting permalink structure"
$WP rewrite structure '/%postname%/'

# ---------- page seeding ----------
#
# seed_page <title> <slug> <content> [old_placeholder]
#   * If page doesn't exist, create with <content>.
#   * If page exists AND its current content equals <old_placeholder>,
#     update to <content>. (Lets us re-seed placeholders on next deploy.)
#   * Otherwise leave existing content alone.

seed_page() {
    local title="$1"
    local slug="$2"
    local content="$3"
    local placeholder="${4-}"

    local id
    id=$($WP post list --post_type=page --name="$slug" --field=ID 2>/dev/null | head -n1 || true)

    if [ -z "$id" ]; then
        log "Creating page: $title (/$slug/)"
        $WP post create --post_type=page --post_status=publish \
            --post_name="$slug" --post_title="$title" \
            --post_content="$content" >/dev/null
        return
    fi

    if [ -n "$placeholder" ]; then
        local current
        current=$($WP post get "$id" --field=post_content || true)
        if [ "$current" = "$placeholder" ] || [ -z "$current" ]; then
            log "Re-seeding placeholder page: /$slug/"
            $WP post update "$id" --post_content="$content" >/dev/null
            return
        fi
    fi

    log "Page exists with custom content, leaving alone: /$slug/"
}

# Common cover-style hero used on inner pages.
make_hero() {
    local eyebrow="$1"
    local headline="$2"
    cat <<HERO
<!-- wp:cover {"customOverlayColor":"#0A2540","minHeight":50,"minHeightUnit":"vh","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-cover" style="padding:5rem 1.5rem;min-height:50vh"><span aria-hidden="true" class="wp-block-cover__background has-background-dim" style="background-color:#0A2540"></span><div class="wp-block-cover__inner-container">
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.85rem","letterSpacing":"0.18em","textTransform":"uppercase","fontWeight":"600"}},"textColor":"gold"} -->
<p class="has-text-align-center has-gold-color has-text-color" style="font-size:0.85rem;font-weight:600;letter-spacing:0.18em;text-transform:uppercase">${eyebrow}</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"textAlign":"center","level":1,"textColor":"cream","style":{"typography":{"fontSize":"clamp(2.5rem,5vw,4.5rem)","fontWeight":"500","lineHeight":"1.05","letterSpacing":"-0.02em"}}} -->
<h1 class="wp-block-heading has-text-align-center has-cream-color has-text-color" style="font-size:clamp(2.5rem,5vw,4.5rem);font-weight:500;line-height:1.05;letter-spacing:-0.02em">${headline}</h1>
<!-- /wp:heading -->
</div></div>
<!-- /wp:cover -->
HERO
}

# Build content blobs.

ABOUT_CONTENT="$(make_hero 'About' "It's her turn.")
<!-- wp:pattern {\"slug\":\"her-next-mission/mission-vision-values\"} /-->
<!-- wp:pattern {\"slug\":\"her-next-mission/true-north\"} /-->"

SERVICES_CONTENT="$(make_hero 'Programs' 'Whole-woman healing.')
<!-- wp:pattern {\"slug\":\"her-next-mission/services-grid\"} /-->
<!-- wp:pattern {\"slug\":\"her-next-mission/sponsor-deck-cta\"} /-->"

PODCAST_CONTENT="$(make_hero 'The Podcast' 'Her voice. Her story. Her next mission.')
<!-- wp:pattern {\"slug\":\"her-next-mission/podcast-feature\"} /-->
<!-- wp:group {\"layout\":{\"type\":\"constrained\",\"contentSize\":\"720px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"4rem\",\"bottom\":\"4rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group\" style=\"padding:4rem 1.5rem\">
<!-- wp:heading {\"level\":2} --><h2 class=\"wp-block-heading\">Episodes</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>New episodes coming soon. To submit a story or guest a future episode, use the form on the homepage.</p><!-- /wp:paragraph -->
</div>
<!-- /wp:group -->"

EVENTS_CONTENT="$(make_hero 'Events &amp; Summit' 'Where the mission gathers.')
<!-- wp:group {\"layout\":{\"type\":\"constrained\",\"contentSize\":\"760px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group\" style=\"padding:5rem 1.5rem\">
<!-- wp:heading {\"level\":2} --><h2 class=\"wp-block-heading\">The Summit</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>An annual gathering of women who served and women now serving themselves — virtual, hybrid, and in-person tracks. Tiered tickets including scholarship seats. Date and venue announced soon.</p><!-- /wp:paragraph -->
<!-- wp:heading {\"level\":2} --><h2 class=\"wp-block-heading\">Retreats &amp; Bootcamps</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Multi-day immersive retreats and cohort-based bootcamps held throughout the year. Schedule announced via email and the podcast.</p><!-- /wp:paragraph -->
</div>
<!-- /wp:group -->"

SPONSOR_DECK_CONTENT="$(make_hero 'Become a Mission Partner' 'Stand with these women.')
<!-- wp:group {\"layout\":{\"type\":\"constrained\",\"contentSize\":\"960px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"3rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group\" style=\"padding:5rem 1.5rem 3rem\">
<!-- wp:heading {\"level\":2,\"textAlign\":\"center\"} --><h2 class=\"wp-block-heading has-text-align-center\">Three tiers. Real activation.</h2><!-- /wp:heading -->
<!-- wp:columns -->
<div class=\"wp-block-columns\">
<!-- wp:column --><div class=\"wp-block-column\"><!-- wp:heading {\"level\":3} --><h3 class=\"wp-block-heading\">Featured Sponsor &mdash; \$25k</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Sponsor page on the site, featured section placement, and recognition across podcast and events.</p><!-- /wp:paragraph --></div><!-- /wp:column -->
<!-- wp:column --><div class=\"wp-block-column\"><!-- wp:heading {\"level\":3} --><h3 class=\"wp-block-heading\">Lead Sponsor &mdash; \$50k</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Table at live events, dedicated page, recognition at speaking events, TV and podcast features.</p><!-- /wp:paragraph --></div><!-- /wp:column -->
<!-- wp:column --><div class=\"wp-block-column\"><!-- wp:heading {\"level\":3} --><h3 class=\"wp-block-heading\">Mission Partner &mdash; \$250k</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Naming rights to a live event with 50+ women, full media and production rights, corporate consulting track, and press leadership access.</p><!-- /wp:paragraph --></div><!-- /wp:column -->
</div>
<!-- /wp:columns -->
<!-- wp:heading {\"level\":2,\"textAlign\":\"center\",\"style\":{\"spacing\":{\"margin\":{\"top\":\"3rem\"}}}} --><h2 class=\"wp-block-heading has-text-align-center\" style=\"margin-top:3rem\">Request the full deck</h2><!-- /wp:heading -->
[hnm_sponsor_deck_form]
</div>
<!-- /wp:group -->"

GIVE_CONTENT="$(make_hero 'Give' 'Move her mission forward.')
<!-- wp:group {\"layout\":{\"type\":\"constrained\",\"contentSize\":\"720px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group\" style=\"padding:5rem 1.5rem\">
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"1.1875rem\",\"lineHeight\":\"1.65\"}}} --><p style=\"font-size:1.1875rem;line-height:1.65\">Every dollar moves a woman through her transition &mdash; coaching, retreats, scholarships to the Summit. Recurring giving is the most impactful way to support these women.</p><!-- /wp:paragraph -->
<!-- wp:buttons --><div class=\"wp-block-buttons\">
<!-- wp:button {\"backgroundColor\":\"navy\",\"textColor\":\"cream\"} --><div class=\"wp-block-button\"><a class=\"wp-block-button__link has-cream-color has-navy-background-color has-text-color has-background wp-element-button\" href=\"#donate\">Give Once</a></div><!-- /wp:button -->
<!-- wp:button {\"backgroundColor\":\"gold\",\"textColor\":\"navy-deep\"} --><div class=\"wp-block-button\"><a class=\"wp-block-button__link has-navy-deep-color has-gold-background-color has-text-color has-background wp-element-button\" href=\"#donate-monthly\">Give Monthly</a></div><!-- /wp:button -->
</div><!-- /wp:buttons -->
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"0.95rem\"}},\"textColor\":\"ink-soft\",\"className\":\"is-style-default\"} --><p class=\"has-ink-soft-color has-text-color\" style=\"font-size:0.95rem\"><em>Configure the donation buttons to point at your Stripe / Donorbox / Givebutter checkout once it's live.</em></p><!-- /wp:paragraph -->
</div>
<!-- /wp:group -->"

CONTACT_CONTENT="$(make_hero 'Contact' 'Reach out.')
<!-- wp:group {\"layout\":{\"type\":\"constrained\",\"contentSize\":\"720px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group\" style=\"padding:5rem 1.5rem\">
<!-- wp:paragraph --><p>For partnership, press, or speaking inquiries: <a href=\"mailto:hello@hernextmission.org\">hello@hernextmission.org</a></p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>To submit a story for the podcast: <a href=\"/podcast/\">visit the podcast page</a>.</p><!-- /wp:paragraph -->
</div>
<!-- /wp:group -->"

BOOK_CALL_CONTENT="$(make_hero 'Book a Call' 'Let&#39;s talk about what&#39;s next.')
<!-- wp:group {\"layout\":{\"type\":\"constrained\",\"contentSize\":\"720px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group\" style=\"padding:5rem 1.5rem\">
<!-- wp:paragraph --><p>Schedule a discovery call to talk about coaching, bootcamps, retreats, or which program fits where you are right now.</p><!-- /wp:paragraph -->
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"0.95rem\"}},\"textColor\":\"ink-soft\"} --><p class=\"has-ink-soft-color has-text-color\" style=\"font-size:0.95rem\"><em>Replace this block with your Calendly or Cal.com embed once you have a scheduling link.</em></p><!-- /wp:paragraph -->
</div>
<!-- /wp:group -->"

SPONSORS_CONTENT="$(make_hero 'Sponsors' 'Stand with these women.')
<!-- wp:group {\"layout\":{\"type\":\"constrained\",\"contentSize\":\"960px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group\" style=\"padding:5rem 1.5rem\">
<!-- wp:paragraph --><p>Sponsor logos and Mission Partners will appear here. Add sponsors via wp-admin &rarr; Sponsors.</p><!-- /wp:paragraph -->
<!-- wp:pattern {\"slug\":\"her-next-mission/sponsor-deck-cta\"} /-->
</div>
<!-- /wp:group -->"

PRIVACY_CONTENT="$(make_hero 'Privacy' 'Your information, handled with care.')
<!-- wp:group {\"layout\":{\"type\":\"constrained\",\"contentSize\":\"720px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group\" style=\"padding:5rem 1.5rem\">
<!-- wp:paragraph --><p>Her Next Mission collects only the information you submit through forms (such as name, email, phone, and company for sponsor deck requests). This information is stored on our servers and shared only with team members directly responsible for your inquiry.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>You can request deletion of your data at any time by contacting us.</p><!-- /wp:paragraph -->
</div>
<!-- /wp:group -->"

# Seed pages. The 4th argument is the OLD placeholder content from v0.1
# — pages whose content still matches that string get re-seeded.

seed_page "About"             "about"             "$ABOUT_CONTENT"        "<p>About Her Next Mission Foundation.</p>"
seed_page "Services"          "services"          "$SERVICES_CONTENT"     "<p>Programs and services.</p>"
seed_page "Podcast"           "podcast"           "$PODCAST_CONTENT"      "<p>The Her Next Mission podcast.</p>"
seed_page "Events"            "events"            "$EVENTS_CONTENT"       "<p>Summit, retreats, and events.</p>"
seed_page "Book a Call"       "book-a-call"       "$BOOK_CALL_CONTENT"    "<p>Book a discovery call.</p>"
seed_page "Sponsors"          "sponsors"          "$SPONSORS_CONTENT"     "<p>Featured sponsors of Her Next Mission.</p>"
seed_page "Sponsor Deck"      "sponsor-deck"      "$SPONSOR_DECK_CONTENT" "[hnm_sponsor_deck_form]"
seed_page "Give"              "give"              "$GIVE_CONTENT"         "<p>Donate to Her Next Mission.</p>"
seed_page "Featured Sponsors" "featured-sponsors" "$SPONSORS_CONTENT"     "<p>Our featured sponsors.</p>"
seed_page "Contact"           "contact"           "$CONTACT_CONTENT"      "<p>Get in touch.</p>"
seed_page "Privacy"           "privacy"           "$PRIVACY_CONTENT"      "<p>Privacy policy.</p>"

# ---------- static front page ----------

if [ "$($WP option get show_on_front)" != "page" ]; then
    log "Setting static front page"
    if ! $WP post list --post_type=page --name="home" --field=ID | grep -q .; then
        $WP post create --post_type=page --post_status=publish --post_name="home" --post_title="Home" --post_content="" >/dev/null
    fi
    HOME_ID=$($WP post list --post_type=page --name="home" --field=ID | head -n1)
    $WP option update show_on_front "page"
    $WP option update page_on_front "$HOME_ID"
fi

log "Done."
