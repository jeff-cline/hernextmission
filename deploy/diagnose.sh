#!/usr/bin/env bash
#
# diagnose.sh — runs against the live site to report exactly what's
# being served. Use this to figure out whether WordPress is still
# alive, whether a deploy actually went through, and whether caches
# are masking the result.
#
# Usage:
#   bash deploy/diagnose.sh           # checks https://hernextmission.org
#   DOMAIN=staging.example.com bash deploy/diagnose.sh

set -u

DOMAIN="${DOMAIN:-hernextmission.org}"
BASE="https://${DOMAIN}"

ok()    { printf '  \033[32m✓\033[0m %s\n' "$*"; }
bad()   { printf '  \033[31m✗\033[0m %s\n' "$*"; }
warn()  { printf '  \033[33m!\033[0m %s\n' "$*"; }
hd()    { printf '\n\033[1m== %s ==\033[0m\n' "$*"; }

# 1. Reach the homepage with cache-busting
hd "1. Homepage reachability"
ts=$(date +%s)
HOME_BODY="$(curl -ksSL -A 'Mozilla/5.0 (HNM-diag)' -H 'Cache-Control: no-cache' "$BASE/?nocache=$ts")"
HOME_CODE="$(curl -ksS -o /dev/null -w '%{http_code}' "$BASE/?nocache=$ts")"
if [ "$HOME_CODE" = "200" ]; then ok "$BASE/ → 200"; else bad "$BASE/ → $HOME_CODE"; fi

# 2. Detect build version
hd "2. Deployed build version"
BUILD="$(echo "$HOME_BODY" | grep -oE 'name="hnm-build" content="[^"]+"' | sed -E 's/.*content="([^"]+)".*/\1/' | head -1)"
if [ -n "$BUILD" ]; then
    ok "Live build marker:   $BUILD"
else
    warn "No <meta name=\"hnm-build\"> found — site is older than v1.5"
fi
# Cache-bust marker on CSS
CSS_VER="$(echo "$HOME_BODY" | grep -oE '/assets/css/site\.css\?v=[^"]+' | sed -E 's/.*\?v=//' | head -1)"
[ -n "$CSS_VER" ] && ok "CSS cache-bust:      ?v=$CSS_VER" || warn "CSS has no ?v= cache-bust — older than v1.5"

# 3. Detect WordPress markers anywhere on the homepage
hd "3. WordPress detection"
WP_MARKERS=0
for needle in "wp-admin" "wp-includes" "wp-content/themes" "wp-content/plugins" "wpemoji" "/?p=" "wp-json" "Generator content=\"WordPress"; do
    if echo "$HOME_BODY" | grep -qi "$needle"; then
        bad "Homepage contains WP marker: $needle"
        WP_MARKERS=$((WP_MARKERS+1))
    fi
done
[ $WP_MARKERS -eq 0 ] && ok "No WordPress markers in homepage HTML"

# Check the WP admin toolbar specifically (looks for #wpadminbar)
if echo "$HOME_BODY" | grep -q "wpadminbar"; then
    bad "Homepage HTML contains #wpadminbar — WordPress is still injecting the admin toolbar"
else
    ok "No #wpadminbar in homepage HTML"
fi

# 4. The classic WP URLs — these should all be 404
hd "4. WordPress URLs (all should be 404)"
for path in "/wp-admin/" "/wp-login.php" "/xmlrpc.php" "/wp-includes/js/wp-emoji-release.min.js" "/?page_id=2"; do
    code="$(curl -ksS -o /dev/null -w '%{http_code}' "$BASE$path")"
    if [ "$code" = "404" ] || [ "$code" = "403" ]; then
        ok "$path → $code"
    else
        bad "$path → $code  (should be 404 — WordPress is still serving it)"
    fi
done

# 5. Server / tech-stack signals
hd "5. Server & tech-stack signals"
SERVER="$(curl -ksSI "$BASE/" | grep -i '^Server:' | tr -d '\r')"
XPB="$(curl -ksSI "$BASE/" | grep -i '^X-Powered-By:' | tr -d '\r')"
[ -n "$SERVER" ] && ok "$SERVER" || warn "No Server header"
if [ -n "$XPB" ]; then bad "$XPB  (PHP is still active — should be removed)"; else ok "No X-Powered-By header (PHP not exposed)"; fi

# 6. Floating compass check
hd "6. Compass-float (deprecated as of v1.3)"
if echo "$HOME_BODY" | grep -q 'class="compass-float"'; then
    bad "Homepage HTML contains <figure class=\"compass-float\"> — site is older than v1.3"
else
    ok "No compass-float element on homepage"
fi

# 7. Pages we shipped in v1.2/v1.3 — confirm they exist
hd "7. New pages reachability (v1.2+)"
for path in "/coaching.html" "/coaching/military-career-coaching.html" "/transition-services.html" "/resources/veteran-identity-disorder.html" "/resources/ptsd.html" "/sitemap.xml" "/llms.txt"; do
    code="$(curl -ksS -o /dev/null -w '%{http_code}' "$BASE$path")"
    if [ "$code" = "200" ]; then ok "$path → 200"; else bad "$path → $code"; fi
done

# 8. Summary
hd "Summary"
printf "  Live build:      %s\n" "${BUILD:-(unknown — pre-v1.5)}"
printf "  CSS cache-bust:  %s\n" "${CSS_VER:-(none)}"
printf "  WP markers:      %s\n" "$WP_MARKERS"
printf "  Local repo HEAD: %s\n" "$(git -C "$(dirname "$0")/.." rev-parse --short HEAD 2>/dev/null || echo unknown)"
printf "\n"
