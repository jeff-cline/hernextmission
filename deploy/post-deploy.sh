#!/usr/bin/env bash
#
# Her Next Mission — post-deploy hook for the server agent.
#
# This script runs the full deploy: it syncs custom code from the repo
# checkout into the WordPress tree, activates theme + plugins, flushes
# rewrites, sets site identity, seeds taxonomy terms, and seeds page
# content with rich nonprofit-grade copy.
#
# Idempotent. Safe to run on every deploy.
#
#   - Pages that don't exist are created.
#   - Pages whose content matches a known placeholder are re-seeded
#     with the latest rich content.
#   - Pages with custom (admin-edited) content are left alone.

set -euo pipefail

WP_ROOT="${WP_ROOT:-/var/www/hernextmission}"

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
$WP option update blogdescription "It's her turn. Coaching, community, and clarity for female Veterans and first responders transitioning out of service."
$WP option update siteurl         "https://hernextmission.org"
$WP option update home            "https://hernextmission.org"
$WP option update timezone_string "America/New_York" --quiet || true

log "Disabling default WP comments (org site, not a blog)"
$WP option update default_comment_status "closed"
$WP option update default_ping_status    "closed"

log "Setting permalink structure"
$WP rewrite structure '/%postname%/'

# ---------- mailto helpers (mirror the PHP catalog) ----------

CTA_EMAIL="krystalore@thecrewscoach.com"

# Generates a mailto: URL for an inline page CTA. Body is short here —
# the rich intake-question version lives in inc/mailto.php and powers
# the pattern CTAs. This helper is just for plain page links.
mailto_url() {
    local title="$1"
    local body="$2"
    python3 - "$CTA_EMAIL" "$title" "$body" <<'PY'
import sys, urllib.parse
email, title, body = sys.argv[1], sys.argv[2], sys.argv[3]
subject = f"{title} - HER NEXT MISSION"
qs = urllib.parse.urlencode({"subject": subject, "body": body}, quote_via=urllib.parse.quote)
print(f"mailto:{email}?{qs}")
PY
}

# ---------- page seeding ----------

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

# Inner-page hero (light, white-dominant, gold eyebrow over navy gradient).
make_hero() {
    local eyebrow="$1"
    local headline="$2"
    cat <<HERO
<!-- wp:cover {"customOverlayColor":"#1F2A52","minHeight":48,"minHeightUnit":"vh","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-cover" style="padding:5rem 1.5rem;min-height:48vh"><span aria-hidden="true" class="wp-block-cover__background has-background-dim" style="background-color:#1F2A52"></span><div class="wp-block-cover__inner-container">
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.85rem","letterSpacing":"0.22em","textTransform":"uppercase","fontWeight":"700"}},"textColor":"gold-soft"} -->
<p class="has-text-align-center has-gold-soft-color has-text-color" style="font-size:0.85rem;font-weight:700;letter-spacing:0.22em;text-transform:uppercase">${eyebrow}</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"textAlign":"center","level":1,"textColor":"paper","style":{"typography":{"fontSize":"clamp(2.75rem,5.5vw,5rem)","fontWeight":"500","lineHeight":"1.05","letterSpacing":"-0.02em"}}} -->
<h1 class="wp-block-heading has-text-align-center has-paper-color has-text-color" style="font-size:clamp(2.75rem,5.5vw,5rem);font-weight:500;line-height:1.05;letter-spacing:-0.02em">${headline}</h1>
<!-- /wp:heading -->
</div></div>
<!-- /wp:cover -->
HERO
}

# ----- mailto CTAs reused across pages -----
EXPLORE_HREF=$(mailto_url "Explore Programs" "I'd like to learn more about the programs Her Next Mission offers.

Branch / agency:
Years of service:
Where you are in your transition:
Programs of interest:

— Your contact info —
Name:
Best phone:
Best email:
City / time zone:
Best time to reach you:")

BOOKCALL_HREF=$(mailto_url "Book a Call" "I'd like to book a call with Krystalore.

Reason for the call:
Top 1–2 things I want to walk away with:
Best three windows of time over the next two weeks (with time zone):

— Your contact info —
Name:
Phone or video preference:
Best phone:
Best email:")

DECK_HREF=$(mailto_url "Request Sponsor Deck" "Please send me the Her Next Mission sponsor deck.

Company / organization:
Sponsor tier(s) of interest (Featured · Lead · Mission Partner):
Use of the deck (executive review, board, marketing committee):
Decision timeline:

— Your contact info —
Name:
Title:
Best phone:
Best email:")

GIVE_HREF=$(mailto_url "Donate" "I'd like to give to Her Next Mission. Please send me information.

Approximate gift size (one-time or recurring):
Restricted vs. unrestricted use:
Personal, family foundation, DAF, or corporate:
Need a tax receipt and 501(c)(3) status letter?

— Your contact info —
Name:
Best phone:
Best email:")

CONTACT_HREF=$(mailto_url "Contact" "Hi Krystalore — I'd like to get in touch.

Reason for reaching out:
Context:

— Your contact info —
Name:
Best phone:
Best email:")

PRESS_HREF=$(mailto_url "Media & Press" "I'm a journalist / producer reaching out for a story.

Outlet:
Topic / angle:
Deadline:
Format (print, broadcast, podcast, panel, written quote):

— Your contact info —
Name:
Title:
Best phone:
Best email:")

VOLUNTEER_HREF=$(mailto_url "Volunteer" "I'd like to volunteer with Her Next Mission.

Background / branch / agency:
Skills you'd love to use:
Hours per month you can commit:
Remote or in-person:

— Your contact info —
Name:
Best phone:
Best email:
City:")

# ----- ABOUT ---------------------------------------------------------------

ABOUT_CONTENT="$(make_hero 'About Her Next Mission' \"Service beyond service.\")
<!-- wp:group {\"className\":\"hnm-section\",\"layout\":{\"type\":\"constrained\",\"contentSize\":\"760px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"3rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group hnm-section\" style=\"padding:5rem 1.5rem 3rem\">
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"1.25rem\",\"lineHeight\":\"1.7\"}}} --><p style=\"font-size:1.25rem;line-height:1.7\">Her Next Mission is a 501(c)(3) nonprofit (in formation) serving female Veterans and first responders in the hardest mission of their career: leaving service. We meet women where they are &mdash; transitioning, retiring, medically separating, or years out and stuck &mdash; with the coaching, community, and clarity that the standard programs miss.</p><!-- /wp:paragraph -->
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"1.0625rem\",\"lineHeight\":\"1.7\"}}} --><p style=\"font-size:1.0625rem;line-height:1.7\">National data tells us why this is needed: 54% of women Veterans report not feeling prepared to navigate civilian community resources (compared with 35% of men), and women Veterans face higher rates of identity loss, isolation, and unemployment in the first 12 months after separation. The same patterns hold for women leaving fire, law enforcement, and EMS &mdash; service cultures shaped around men, with few off-ramps that look like them.</p><!-- /wp:paragraph -->
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"1.0625rem\",\"lineHeight\":\"1.7\"}}} --><p style=\"font-size:1.0625rem;line-height:1.7\">We exist to close that gap. Through 1-on-1 coaching, cohort bootcamps, somatic-fitness retreats, the annual Summit, and the <em>From Service to Success</em> podcast, we walk alongside women as they reclaim their identity, rebuild their confidence, and discover what comes next.</p><!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:pattern {\"slug\":\"her-next-mission/mission-vision-values\"} /-->
<!-- wp:pattern {\"slug\":\"her-next-mission/true-north\"} /-->
<!-- wp:group {\"className\":\"hnm-section\",\"backgroundColor\":\"cream\",\"layout\":{\"type\":\"constrained\",\"contentSize\":\"880px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"6rem\",\"bottom\":\"6rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group hnm-section has-cream-background-color has-background\" style=\"padding:6rem 1.5rem\">
<!-- wp:html --><p style=\"text-align:center;margin:0 0 0.75rem\"><span class=\"hnm-eyebrow\">Founder</span></p><!-- /wp:html -->
<!-- wp:heading {\"level\":2,\"textAlign\":\"center\"} --><h2 class=\"wp-block-heading has-text-align-center\">SMSgt Krystalore Crews, USAF (Ret.)</h2><!-- /wp:heading -->
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"1.0625rem\",\"lineHeight\":\"1.7\"}}} --><p style=\"font-size:1.0625rem;line-height:1.7\">Krystalore served 22 years in the U.S. Air Force and Air National Guard, retiring in 2024 as a Senior Master Sergeant. The transition out of uniform was the hardest mission of her career &mdash; and the catalyst for this work. She is a certified life and somatic coach, founder, podcast host, and a relentless advocate for the women who served alongside her.</p><!-- /wp:paragraph -->
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"1.0625rem\",\"lineHeight\":\"1.7\"}}} --><p style=\"font-size:1.0625rem;line-height:1.7\">Her work blends the discipline of 22 years in uniform with somatic and nervous-system tools that meet the body where the mind can't go alone. The result is a brave space for women to do the work most programs skip.</p><!-- /wp:paragraph -->
<!-- wp:buttons {\"layout\":{\"type\":\"flex\",\"justifyContent\":\"center\"}} --><div class=\"wp-block-buttons\"><!-- wp:button --><div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"${BOOKCALL_HREF}\">Book a Call with Krystalore</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
</div>
<!-- /wp:group -->"

# ----- SERVICES / PROGRAMS -------------------------------------------------

SERVICES_CONTENT="$(make_hero 'Programs' 'Whole-woman healing.')
<!-- wp:group {\"className\":\"hnm-section\",\"layout\":{\"type\":\"constrained\",\"contentSize\":\"880px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"3rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group hnm-section\" style=\"padding:5rem 1.5rem 3rem\">
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"1.1875rem\",\"lineHeight\":\"1.7\"}}} --><p style=\"font-size:1.1875rem;line-height:1.7\">Six entry points into Her Next Mission. Most women come in through one and stay for the rest. Pricing is sliding-scale where applicable; scholarship support is always available so cost is never the gate.</p><!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:pattern {\"slug\":\"her-next-mission/services-grid\"} /-->
<!-- wp:group {\"className\":\"hnm-section\",\"backgroundColor\":\"cream\",\"layout\":{\"type\":\"constrained\",\"contentSize\":\"880px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"6rem\",\"bottom\":\"6rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group hnm-section has-cream-background-color has-background\" style=\"padding:6rem 1.5rem\">
<!-- wp:heading {\"level\":2,\"textAlign\":\"center\"} --><h2 class=\"wp-block-heading has-text-align-center\">How we work</h2><!-- /wp:heading -->
<!-- wp:list {\"className\":\"hnm-compass-list\",\"style\":{\"typography\":{\"fontSize\":\"1.0625rem\",\"lineHeight\":\"1.7\"}}} -->
<ul class=\"hnm-compass-list\" style=\"font-size:1.0625rem;line-height:1.7\">
<li><strong>Somatic + nervous-system coaching.</strong> Identity work that reaches the places talk therapy can't.</li>
<li><strong>Cohort containers.</strong> No woman crosses this threshold alone; bootcamps and retreats run as small, brave groups.</li>
<li><strong>Whole-woman care.</strong> Mind, body, spirit &mdash; coaching, fitness, sleep, nutrition, faith if she wants it.</li>
<li><strong>Sliding scale.</strong> Scholarship seats funded by donors and sponsors so service-status never gates access.</li>
<li><strong>Plain-English transition mapping.</strong> Benefits, careers, and entrepreneurship paths translated for women coming out of uniform.</li>
<li><strong>Podcast + Summit.</strong> Stories, language, and proof that the next mission is real.</li>
</ul>
<!-- /wp:list -->
<!-- wp:buttons {\"layout\":{\"type\":\"flex\",\"justifyContent\":\"center\"}} --><div class=\"wp-block-buttons\"><!-- wp:button --><div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"${EXPLORE_HREF}\">Explore Programs</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
</div>
<!-- /wp:group -->
<!-- wp:pattern {\"slug\":\"her-next-mission/sponsor-deck-cta\"} /-->"

# ----- PODCAST -------------------------------------------------------------

PODCAST_GUEST_HREF=$(mailto_url "Submit Your Story" "I'd like to share my story on the Her Next Mission podcast.

Branch / agency:
In one sentence — the story I want to tell:
Why now?
Topics I can speak on:

— Your contact info —
Name:
Best phone:
Best email:
LinkedIn / IG / prior interview:")

PODCAST_NEWS_HREF=$(mailto_url "Join the Newsletter" "I'd like to join the Her Next Mission newsletter and episode-update list.

Are you a transitioning servicemember, sponsor, donor, or supporter?

— Your contact info —
Name:
Best email:")

PODCAST_CONTENT="$(make_hero 'The Podcast' 'Her voice. Her story. Her next mission.')
<!-- wp:pattern {\"slug\":\"her-next-mission/podcast-feature\"} /-->
<!-- wp:group {\"className\":\"hnm-section\",\"layout\":{\"type\":\"constrained\",\"contentSize\":\"880px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group hnm-section\" style=\"padding:5rem 1.5rem\">
<!-- wp:html --><p style=\"margin:0 0 0.75rem\"><span class=\"hnm-eyebrow\">What we cover</span></p><!-- /wp:html -->
<!-- wp:heading {\"level\":2} --><h2 class=\"wp-block-heading\">Five themes. Real conversations.</h2><!-- /wp:heading -->
<!-- wp:list {\"className\":\"hnm-compass-list\",\"style\":{\"typography\":{\"fontSize\":\"1.0625rem\",\"lineHeight\":\"1.7\"}}} -->
<ul class=\"hnm-compass-list\" style=\"font-size:1.0625rem;line-height:1.7\">
<li><strong>Transition.</strong> The 365 days before separation and the 365 days after &mdash; what no one prepared us for.</li>
<li><strong>Wellness.</strong> Nervous system, sleep, somatic work, healing the body the uniform asked you to override.</li>
<li><strong>Business.</strong> Launching a service, consultancy, or 501(c)(3) on the other side of the uniform.</li>
<li><strong>Leadership.</strong> Taking what you learned commanding a team and leading yourself, your family, your second chapter.</li>
<li><strong>Retreats &amp; community.</strong> Why women heal in proximity to other women who have stood where they stand.</li>
</ul>
<!-- /wp:list -->
<!-- wp:heading {\"level\":2,\"style\":{\"spacing\":{\"margin\":{\"top\":\"3rem\"}}}} --><h2 class=\"wp-block-heading\" style=\"margin-top:3rem\">Episodes</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>New episodes drop monthly. Until the player is wired up here, subscribe via the newsletter to be first to know when each episode is live.</p><!-- /wp:paragraph -->
<!-- wp:buttons --><div class=\"wp-block-buttons\">
<!-- wp:button --><div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"${PODCAST_NEWS_HREF}\">Get Episode Updates</a></div><!-- /wp:button -->
<!-- wp:button {\"className\":\"is-style-outline\"} --><div class=\"wp-block-button is-style-outline\"><a class=\"wp-block-button__link wp-element-button\" href=\"${PODCAST_GUEST_HREF}\">Submit Your Story</a></div><!-- /wp:button -->
</div><!-- /wp:buttons -->
</div>
<!-- /wp:group -->"

# ----- EVENTS / SUMMIT -----------------------------------------------------

SUMMIT_HREF=$(mailto_url "Summit & Events" "I'd like more information about the Her Next Mission Summit and live events.

Attendee / speaker / sponsor / volunteer:
Branch / agency:
City you'd fly from:
Need scholarship support?

— Your contact info —
Name:
Best phone:
Best email:")

RETREAT_HREF=$(mailto_url "Retreats" "I'd like more information on Her Next Mission retreats.

Branch / agency:
Which retreat are you drawn to?
Physical considerations to plan around:
Solo or with a battle buddy?
Need scholarship support?

— Your contact info —
Name:
Best phone:
Best email:
City:")

BOOTCAMP_HREF=$(mailto_url "Group Bootcamps" "I'm interested in the next Her Next Mission bootcamp cohort.

Branch / agency:
Bootcamp of interest (clarity / business / leadership):
Why now:
Time-zone:

— Your contact info —
Name:
Best phone:
Best email:")

EVENTS_CONTENT="$(make_hero 'Events &amp; Summit' 'Where the mission gathers.')
<!-- wp:group {\"className\":\"hnm-section\",\"layout\":{\"type\":\"constrained\",\"contentSize\":\"880px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"3rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group hnm-section\" style=\"padding:5rem 1.5rem 3rem\">
<!-- wp:html --><p style=\"margin:0 0 0.75rem\"><span class=\"hnm-eyebrow\">The Annual Summit</span></p><!-- /wp:html -->
<!-- wp:heading {\"level\":2} --><h2 class=\"wp-block-heading\">From Service to Success Summit</h2><!-- /wp:heading -->
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"1.125rem\",\"lineHeight\":\"1.7\"}}} --><p style=\"font-size:1.125rem;line-height:1.7\">A flagship gathering of women who served and women now serving themselves &mdash; Veterans, first responders, sponsors, and the leaders who fund the work. In-person, virtual, and hybrid tracks. Tiered tickets including scholarship seats so service-status never gates access. Date and venue announced soon.</p><!-- /wp:paragraph -->
<!-- wp:list {\"className\":\"hnm-compass-list\",\"style\":{\"typography\":{\"fontSize\":\"1.0625rem\",\"lineHeight\":\"1.7\"}}} --><ul class=\"hnm-compass-list\" style=\"font-size:1.0625rem;line-height:1.7\">
<li>Keynotes from women who built second careers after the uniform</li>
<li>Working sessions on transition, business launch, leadership, and somatic healing</li>
<li>Sponsor activation tracks for corporate partners</li>
<li>Press &amp; media room for Veteran storytelling</li>
</ul><!-- /wp:list -->
<!-- wp:buttons --><div class=\"wp-block-buttons\"><!-- wp:button --><div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"${SUMMIT_HREF}\">Get Summit Info</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
</div>
<!-- /wp:group -->
<!-- wp:group {\"className\":\"hnm-section\",\"backgroundColor\":\"cream\",\"layout\":{\"type\":\"constrained\",\"contentSize\":\"880px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"6rem\",\"bottom\":\"6rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group hnm-section has-cream-background-color has-background\" style=\"padding:6rem 1.5rem\">
<!-- wp:html --><p style=\"margin:0 0 0.75rem\"><span class=\"hnm-eyebrow\">Retreats &amp; Bootcamps</span></p><!-- /wp:html -->
<!-- wp:heading {\"level\":2} --><h2 class=\"wp-block-heading\">Smaller, deeper, year-round.</h2><!-- /wp:heading -->
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"1.0625rem\",\"lineHeight\":\"1.7\"}}} --><p style=\"font-size:1.0625rem;line-height:1.7\">Multi-day immersive retreats &mdash; somatic fitness, breathwork, sleep restoration, and the kind of bonds forged in service &mdash; alongside cohort bootcamps for clarity, business launch, and leadership.</p><!-- /wp:paragraph -->
<!-- wp:buttons --><div class=\"wp-block-buttons\">
<!-- wp:button --><div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"${RETREAT_HREF}\">Reserve a Retreat Spot</a></div><!-- /wp:button -->
<!-- wp:button {\"className\":\"is-style-outline\"} --><div class=\"wp-block-button is-style-outline\"><a class=\"wp-block-button__link wp-element-button\" href=\"${BOOTCAMP_HREF}\">Join Next Bootcamp</a></div><!-- /wp:button -->
</div><!-- /wp:buttons -->
</div>
<!-- /wp:group -->"

# ----- SPONSORS ------------------------------------------------------------

SPONSORS_CONTENT="$(make_hero 'Sponsors' 'Stand with these women.')
<!-- wp:group {\"className\":\"hnm-section\",\"layout\":{\"type\":\"constrained\",\"contentSize\":\"960px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"3rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group hnm-section\" style=\"padding:5rem 1.5rem 3rem\">
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"1.1875rem\",\"lineHeight\":\"1.7\"}}} --><p style=\"font-size:1.1875rem;line-height:1.7\">Three tiers, real activation. We design partnerships around what your team needs to report and the audience your brand wants to stand with &mdash; women in service, women in transition, and the families and employers who back them.</p><!-- /wp:paragraph -->
<!-- wp:columns -->
<div class=\"wp-block-columns\">
<!-- wp:column --><div class=\"wp-block-column\"><!-- wp:group {\"className\":\"hnm-card\",\"style\":{\"spacing\":{\"padding\":{\"top\":\"2rem\",\"bottom\":\"2rem\",\"left\":\"1.75rem\",\"right\":\"1.75rem\"}}}} --><div class=\"wp-block-group hnm-card\" style=\"padding:2rem 1.75rem\"><!-- wp:html --><p style=\"margin:0 0 0.5rem\"><span class=\"hnm-eyebrow\">Featured Sponsor</span></p><!-- /wp:html --><!-- wp:heading {\"level\":3} --><h3 class=\"wp-block-heading\">Featured Sponsor</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Featured placement on the site, recognition across the podcast, and visibility at one annual event.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column -->
<!-- wp:column --><div class=\"wp-block-column\"><!-- wp:group {\"className\":\"hnm-card\",\"backgroundColor\":\"navy\",\"textColor\":\"cream\",\"style\":{\"spacing\":{\"padding\":{\"top\":\"2rem\",\"bottom\":\"2rem\",\"left\":\"1.75rem\",\"right\":\"1.75rem\"}}}} --><div class=\"wp-block-group hnm-card has-cream-color has-navy-background-color has-text-color has-background\" style=\"padding:2rem 1.75rem\"><!-- wp:html --><p style=\"margin:0 0 0.5rem;color:#E8C870;font-size:0.78rem;font-weight:700;letter-spacing:0.22em;text-transform:uppercase\">Lead Sponsor</p><!-- /wp:html --><!-- wp:heading {\"level\":3,\"textColor\":\"cream\"} --><h3 class=\"wp-block-heading has-cream-color has-text-color\">Lead Sponsor</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Reserved table at live events, dedicated landing page, recognition at speaking events, and podcast / TV features.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column -->
<!-- wp:column --><div class=\"wp-block-column\"><!-- wp:group {\"className\":\"hnm-card\",\"style\":{\"spacing\":{\"padding\":{\"top\":\"2rem\",\"bottom\":\"2rem\",\"left\":\"1.75rem\",\"right\":\"1.75rem\"}}}} --><div class=\"wp-block-group hnm-card\" style=\"padding:2rem 1.75rem\"><!-- wp:html --><p style=\"margin:0 0 0.5rem\"><span class=\"hnm-eyebrow\">Mission Partner</span></p><!-- /wp:html --><!-- wp:heading {\"level\":3} --><h3 class=\"wp-block-heading\">Mission Partner</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Naming rights to a live event with 50+ women, full media and production rights, corporate consulting track, and press leadership access.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column -->
</div>
<!-- /wp:columns -->
<!-- wp:heading {\"level\":2,\"textAlign\":\"center\",\"style\":{\"spacing\":{\"margin\":{\"top\":\"3rem\"}}}} --><h2 class=\"wp-block-heading has-text-align-center\" style=\"margin-top:3rem\">Activations we already deliver</h2><!-- /wp:heading -->
<!-- wp:list {\"className\":\"hnm-compass-list\",\"style\":{\"typography\":{\"fontSize\":\"1.0625rem\",\"lineHeight\":\"1.7\"}}} -->
<ul class=\"hnm-compass-list\" style=\"font-size:1.0625rem;line-height:1.7\">
<li>Brand-safe podcast reads on <em>From Service to Success</em></li>
<li>Live-event naming, stage time, and on-site brand activation</li>
<li>Co-branded content series spotlighting beneficiaries (with consent)</li>
<li>Corporate consulting track &mdash; bring your team to a leadership offsite with our facilitators</li>
<li>Veteran-impact reporting your DEI/ESG team can use</li>
</ul>
<!-- /wp:list -->
<!-- wp:buttons {\"layout\":{\"type\":\"flex\",\"justifyContent\":\"center\"}} --><div class=\"wp-block-buttons\"><!-- wp:button --><div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"${DECK_HREF}\">Request Sponsor Deck</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
</div>
<!-- /wp:group -->"

# ----- SPONSOR DECK form-page ----------------------------------------------

SPONSOR_DECK_CONTENT="$(make_hero 'Become a Mission Partner' 'Stand with these women.')
<!-- wp:group {\"className\":\"hnm-section\",\"layout\":{\"type\":\"constrained\",\"contentSize\":\"720px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group hnm-section\" style=\"padding:5rem 1.5rem\">
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"1.1875rem\",\"lineHeight\":\"1.7\"}}} --><p style=\"font-size:1.1875rem;line-height:1.7\">Request the full sponsor deck. We'll follow up within two business days with the deck, recent impact numbers, and three open partnership slots.</p><!-- /wp:paragraph -->
[hnm_sponsor_deck_form]
</div>
<!-- /wp:group -->"

# ----- GIVE ----------------------------------------------------------------

GIVE_CONTENT="$(make_hero 'Give' 'Move her mission forward.')
<!-- wp:group {\"className\":\"hnm-section\",\"layout\":{\"type\":\"constrained\",\"contentSize\":\"880px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"3rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group hnm-section\" style=\"padding:5rem 1.5rem 3rem\">
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"1.1875rem\",\"lineHeight\":\"1.7\"}}} --><p style=\"font-size:1.1875rem;line-height:1.7\">Every dollar moves a woman through her transition &mdash; coaching, retreats, scholarships to the Summit. Recurring giving is the most impactful way to support these women, because programs are built around what we can sustain.</p><!-- /wp:paragraph -->
<!-- wp:heading {\"level\":2} --><h2 class=\"wp-block-heading\">Where your gift goes</h2><!-- /wp:heading -->
<!-- wp:list {\"className\":\"hnm-compass-list\",\"style\":{\"typography\":{\"fontSize\":\"1.0625rem\",\"lineHeight\":\"1.7\"}}} -->
<ul class=\"hnm-compass-list\" style=\"font-size:1.0625rem;line-height:1.7\">
<li><strong>Coaching scholarships.</strong> 1-on-1 sessions for women who otherwise couldn't access them.</li>
<li><strong>Bootcamp seats.</strong> A full cohort experience for a woman in transition.</li>
<li><strong>Retreats.</strong> Sending a woman to a multi-day immersive retreat, fully covered.</li>
<li><strong>Regional micro-Summits.</strong> Bringing the work into your city for the women already there.</li>
<li><strong>Named gifts.</strong> Funding an entire program track or live event in someone's honor.</li>
</ul>
<!-- /wp:list -->
<!-- wp:heading {\"level\":2,\"style\":{\"spacing\":{\"margin\":{\"top\":\"2rem\"}}}} --><h2 class=\"wp-block-heading\" style=\"margin-top:2rem\">Ways to give</h2><!-- /wp:heading -->
<!-- wp:list {\"className\":\"hnm-compass-list\",\"style\":{\"typography\":{\"fontSize\":\"1.0625rem\",\"lineHeight\":\"1.7\"}}} -->
<ul class=\"hnm-compass-list\" style=\"font-size:1.0625rem;line-height:1.7\">
<li>Recurring monthly giving (most impactful)</li>
<li>One-time gift</li>
<li>Donor-advised fund (DAF) grant</li>
<li>Corporate matching gift</li>
<li>Stock or appreciated-asset transfer</li>
<li>Estate / planned giving</li>
</ul>
<!-- /wp:list -->
<!-- wp:buttons --><div class=\"wp-block-buttons\"><!-- wp:button --><div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"${GIVE_HREF}\">Give Now</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"0.95rem\"}},\"textColor\":\"ink-soft\"} --><p class=\"has-ink-soft-color has-text-color\" style=\"font-size:0.95rem\"><em>Stripe / Donorbox / Givebutter checkout will replace the email-handoff once live. Tax receipt and 501(c)(3) status letter sent within 24 hours of any gift.</em></p><!-- /wp:paragraph -->
</div>
<!-- /wp:group -->"

# ----- BOOK A CALL ---------------------------------------------------------

BOOK_CALL_CONTENT="$(make_hero 'Book a Call' \"Let's talk about what's next.\")
<!-- wp:group {\"className\":\"hnm-section\",\"layout\":{\"type\":\"constrained\",\"contentSize\":\"760px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group hnm-section\" style=\"padding:5rem 1.5rem\">
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"1.1875rem\",\"lineHeight\":\"1.7\"}}} --><p style=\"font-size:1.1875rem;line-height:1.7\">Discovery calls are 30 minutes. We use them to figure out which program fits where you are right now &mdash; coaching, bootcamp, retreat, or none of the above. There's no pitch.</p><!-- /wp:paragraph -->
<!-- wp:heading {\"level\":2} --><h2 class=\"wp-block-heading\">What to expect</h2><!-- /wp:heading -->
<!-- wp:list {\"className\":\"hnm-compass-list\",\"style\":{\"typography\":{\"fontSize\":\"1.0625rem\",\"lineHeight\":\"1.7\"}}} -->
<ul class=\"hnm-compass-list\" style=\"font-size:1.0625rem;line-height:1.7\">
<li>10 minutes on you &mdash; service, transition, what's loud right now</li>
<li>10 minutes on what we offer and what fits</li>
<li>10 minutes on next steps &mdash; or a clean &ldquo;not yet&rdquo; if that's the truth</li>
</ul>
<!-- /wp:list -->
<!-- wp:buttons --><div class=\"wp-block-buttons\"><!-- wp:button --><div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"${BOOKCALL_HREF}\">Email Krystalore to Book</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"0.95rem\"}},\"textColor\":\"ink-soft\"} --><p class=\"has-ink-soft-color has-text-color\" style=\"font-size:0.95rem\"><em>Calendly / Cal.com embed will replace the email handoff once live.</em></p><!-- /wp:paragraph -->
</div>
<!-- /wp:group -->"

# ----- CONTACT -------------------------------------------------------------

CONTACT_CONTENT="$(make_hero 'Contact' \"Pick the right door.\")
<!-- wp:group {\"className\":\"hnm-section\",\"layout\":{\"type\":\"constrained\",\"contentSize\":\"880px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group hnm-section\" style=\"padding:5rem 1.5rem\">
<!-- wp:paragraph {\"style\":{\"typography\":{\"fontSize\":\"1.1875rem\",\"lineHeight\":\"1.7\"}}} --><p style=\"font-size:1.1875rem;line-height:1.7\">Pick the door that matches what you need. Each one opens a tailored email so we can come back to you ready.</p><!-- /wp:paragraph -->
<!-- wp:columns -->
<div class=\"wp-block-columns\">
<!-- wp:column --><div class=\"wp-block-column\"><!-- wp:group {\"className\":\"hnm-card\",\"style\":{\"spacing\":{\"padding\":{\"top\":\"1.5rem\",\"bottom\":\"1.5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} --><div class=\"wp-block-group hnm-card\" style=\"padding:1.5rem\"><!-- wp:heading {\"level\":3,\"style\":{\"typography\":{\"fontSize\":\"1.125rem\"}}} --><h3 class=\"wp-block-heading\" style=\"font-size:1.125rem\">For women in transition</h3><!-- /wp:heading --><!-- wp:paragraph --><p><a href=\"${EXPLORE_HREF}\">Explore programs &rarr;</a></p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column -->
<!-- wp:column --><div class=\"wp-block-column\"><!-- wp:group {\"className\":\"hnm-card\",\"style\":{\"spacing\":{\"padding\":{\"top\":\"1.5rem\",\"bottom\":\"1.5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} --><div class=\"wp-block-group hnm-card\" style=\"padding:1.5rem\"><!-- wp:heading {\"level\":3,\"style\":{\"typography\":{\"fontSize\":\"1.125rem\"}}} --><h3 class=\"wp-block-heading\" style=\"font-size:1.125rem\">Sponsors</h3><!-- /wp:heading --><!-- wp:paragraph --><p><a href=\"${DECK_HREF}\">Request sponsor deck &rarr;</a></p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column -->
<!-- wp:column --><div class=\"wp-block-column\"><!-- wp:group {\"className\":\"hnm-card\",\"style\":{\"spacing\":{\"padding\":{\"top\":\"1.5rem\",\"bottom\":\"1.5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} --><div class=\"wp-block-group hnm-card\" style=\"padding:1.5rem\"><!-- wp:heading {\"level\":3,\"style\":{\"typography\":{\"fontSize\":\"1.125rem\"}}} --><h3 class=\"wp-block-heading\" style=\"font-size:1.125rem\">Donors</h3><!-- /wp:heading --><!-- wp:paragraph --><p><a href=\"${GIVE_HREF}\">Donate &rarr;</a></p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column -->
</div>
<!-- /wp:columns -->
<!-- wp:columns -->
<div class=\"wp-block-columns\">
<!-- wp:column --><div class=\"wp-block-column\"><!-- wp:group {\"className\":\"hnm-card\",\"style\":{\"spacing\":{\"padding\":{\"top\":\"1.5rem\",\"bottom\":\"1.5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} --><div class=\"wp-block-group hnm-card\" style=\"padding:1.5rem\"><!-- wp:heading {\"level\":3,\"style\":{\"typography\":{\"fontSize\":\"1.125rem\"}}} --><h3 class=\"wp-block-heading\" style=\"font-size:1.125rem\">Press &amp; media</h3><!-- /wp:heading --><!-- wp:paragraph --><p><a href=\"${PRESS_HREF}\">Pitch a story &rarr;</a></p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column -->
<!-- wp:column --><div class=\"wp-block-column\"><!-- wp:group {\"className\":\"hnm-card\",\"style\":{\"spacing\":{\"padding\":{\"top\":\"1.5rem\",\"bottom\":\"1.5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} --><div class=\"wp-block-group hnm-card\" style=\"padding:1.5rem\"><!-- wp:heading {\"level\":3,\"style\":{\"typography\":{\"fontSize\":\"1.125rem\"}}} --><h3 class=\"wp-block-heading\" style=\"font-size:1.125rem\">Volunteer</h3><!-- /wp:heading --><!-- wp:paragraph --><p><a href=\"${VOLUNTEER_HREF}\">Lend your skills &rarr;</a></p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column -->
<!-- wp:column --><div class=\"wp-block-column\"><!-- wp:group {\"className\":\"hnm-card\",\"style\":{\"spacing\":{\"padding\":{\"top\":\"1.5rem\",\"bottom\":\"1.5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} --><div class=\"wp-block-group hnm-card\" style=\"padding:1.5rem\"><!-- wp:heading {\"level\":3,\"style\":{\"typography\":{\"fontSize\":\"1.125rem\"}}} --><h3 class=\"wp-block-heading\" style=\"font-size:1.125rem\">Anything else</h3><!-- /wp:heading --><!-- wp:paragraph --><p><a href=\"${CONTACT_HREF}\">Send a note &rarr;</a></p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->"

# ----- PRIVACY -------------------------------------------------------------

PRIVACY_CONTENT="$(make_hero 'Privacy' 'Your information, handled with care.')
<!-- wp:group {\"className\":\"hnm-section\",\"layout\":{\"type\":\"constrained\",\"contentSize\":\"760px\"},\"style\":{\"spacing\":{\"padding\":{\"top\":\"5rem\",\"bottom\":\"5rem\",\"left\":\"1.5rem\",\"right\":\"1.5rem\"}}}} -->
<div class=\"wp-block-group hnm-section\" style=\"padding:5rem 1.5rem\">
<!-- wp:paragraph --><p>Her Next Mission collects only the information you submit through forms and email handoffs (such as name, email, phone, and company for sponsor deck requests). This information is stored on our servers and shared only with team members directly responsible for your inquiry.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>We never sell donor or beneficiary data. Donations are processed through PCI-compliant providers; we do not store payment-card numbers.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>You can request export or deletion of your data at any time by emailing <a href=\"${CONTACT_HREF}\">krystalore@thecrewscoach.com</a>.</p><!-- /wp:paragraph -->
</div>
<!-- /wp:group -->"

# Seed pages.

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
