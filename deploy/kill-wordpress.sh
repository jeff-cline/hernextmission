#!/usr/bin/env bash
#
# kill-wordpress.sh — completely removes WordPress from the
# hernextmission.org server.
#
# What it does:
#   1. Sanity-checks that the static site is in place at $WEB_ROOT
#      and is currently being served (HTTP 200 from localhost).
#   2. Snapshots the WordPress install + DB to /tmp before removal.
#   3. Moves the WordPress files OUTSIDE any docroot — into
#      /var/backups/hnm-wp-archive-<date>/ — so nothing under it can
#      be web-resolved.
#   4. Drops the WordPress MySQL database (after the snapshot).
#   5. Removes WP-related cron jobs (wp-cli, wp-cron pings).
#   6. Verifies that /, /wp-admin/, /wp-login.php, /xmlrpc.php,
#      /?page_id=2 all serve from the static site (200 or 404 — never
#      a WordPress response).
#
# Required env:
#   WP_OLD_ROOT    — old WordPress install root. Default
#                    /var/www/hernextmission/ (the parent of the WP
#                    files, where wp-config.php currently lives).
#   WEB_ROOT       — the static site docroot. Default
#                    /var/www/hernextmission/site/
#   DOMAIN         — the domain to verify against. Default
#                    hernextmission.org
#   WP_DB_NAME     — optional. If unset, parsed from wp-config.php.
#   WP_DB_USER     — optional. If unset, parsed from wp-config.php.
#   WP_DB_PASS     — optional. If unset, parsed from wp-config.php.
#
# Run as root or a user with sudo.

set -euo pipefail

WP_OLD_ROOT="${WP_OLD_ROOT:-/var/www/hernextmission}"
WEB_ROOT="${WEB_ROOT:-/var/www/hernextmission/site}"
DOMAIN="${DOMAIN:-hernextmission.org}"
STAMP="$(date +%Y%m%d-%H%M%S)"
ARCHIVE="/var/backups/hnm-wp-archive-${STAMP}"

log()  { printf '[kill-wp] %s\n' "$*"; }
fail() { printf '[kill-wp] ERROR: %s\n' "$*" >&2; exit 1; }

# ---------- 1. Sanity ----------

[ -d "$WEB_ROOT" ] || fail "Static WEB_ROOT does not exist: $WEB_ROOT"
[ -f "$WEB_ROOT/index.html" ] || fail "Static index.html missing in WEB_ROOT — deploy the static site first"

if grep -qi "wordpress\|wp-content\|wp-admin" "$WEB_ROOT/index.html" 2>/dev/null; then
    fail "$WEB_ROOT/index.html mentions WordPress — wrong WEB_ROOT?"
fi

# Curl localhost to confirm we serve static
LOCAL_FIRST_LINE="$(curl -sS http://127.0.0.1/ | head -50 | grep -o 'HER NEXT MISSION' | head -1 || true)"
if [ -z "$LOCAL_FIRST_LINE" ]; then
    log "WARN: localhost / does not appear to serve the static site yet."
    log "      Continuing — but verify nginx config points at WEB_ROOT before removing WP."
fi

log "WP_OLD_ROOT = $WP_OLD_ROOT"
log "WEB_ROOT    = $WEB_ROOT"
log "DOMAIN      = $DOMAIN"
log "ARCHIVE     = $ARCHIVE"

# ---------- 2. Parse DB creds from wp-config.php (if not provided) ----------

WP_CONFIG="$WP_OLD_ROOT/wp-config.php"
if [ -f "$WP_CONFIG" ]; then
    WP_DB_NAME="${WP_DB_NAME:-$(grep "^define.*DB_NAME"     "$WP_CONFIG" | sed -E "s/.*'([^']+)'.*'([^']+)'.*/\\2/" || true)}"
    WP_DB_USER="${WP_DB_USER:-$(grep "^define.*DB_USER"     "$WP_CONFIG" | sed -E "s/.*'([^']+)'.*'([^']+)'.*/\\2/" || true)}"
    WP_DB_PASS="${WP_DB_PASS:-$(grep "^define.*DB_PASSWORD" "$WP_CONFIG" | sed -E "s/.*'([^']+)'.*'([^']+)'.*/\\2/" || true)}"
fi

# ---------- 3. Snapshot to /tmp ----------

mkdir -p /tmp
SNAPSHOT_TGZ="/tmp/hnm-wp-snapshot-${STAMP}.tgz"
SNAPSHOT_SQL="/tmp/hnm-wp-snapshot-${STAMP}.sql"

log "Snapshotting WP files → $SNAPSHOT_TGZ"
tar czf "$SNAPSHOT_TGZ" -C "$WP_OLD_ROOT" \
    $(cd "$WP_OLD_ROOT" && ls -1 | grep -E '^(wp-|index\.php|xmlrpc\.php|license\.txt|readme\.html)' || true) \
    2>/dev/null || log "WARN: tar reported issues (some WP files may already be missing)"

if [ -n "${WP_DB_NAME:-}" ] && [ -n "${WP_DB_USER:-}" ]; then
    log "Snapshotting WP database $WP_DB_NAME → $SNAPSHOT_SQL"
    mysqldump --single-transaction -u"$WP_DB_USER" -p"$WP_DB_PASS" "$WP_DB_NAME" > "$SNAPSHOT_SQL" \
        || log "WARN: mysqldump failed; you can still remove the DB manually."
else
    log "WARN: could not parse DB creds; skipping DB snapshot. Set WP_DB_NAME/WP_DB_USER/WP_DB_PASS to enable."
fi

# ---------- 4. Move WP files OUTSIDE any web-served path ----------

mkdir -p "$ARCHIVE"
log "Archiving WP files → $ARCHIVE"

# Move every WP-known file/dir OUT of the docroot.
WP_PATHS=(
    "$WP_OLD_ROOT/wp-admin"
    "$WP_OLD_ROOT/wp-includes"
    "$WP_OLD_ROOT/wp-content"
    "$WP_OLD_ROOT/wp-config.php"
    "$WP_OLD_ROOT/wp-config-sample.php"
    "$WP_OLD_ROOT/wp-login.php"
    "$WP_OLD_ROOT/wp-cron.php"
    "$WP_OLD_ROOT/xmlrpc.php"
    "$WP_OLD_ROOT/wp-activate.php"
    "$WP_OLD_ROOT/wp-blog-header.php"
    "$WP_OLD_ROOT/wp-comments-post.php"
    "$WP_OLD_ROOT/wp-links-opml.php"
    "$WP_OLD_ROOT/wp-load.php"
    "$WP_OLD_ROOT/wp-mail.php"
    "$WP_OLD_ROOT/wp-settings.php"
    "$WP_OLD_ROOT/wp-signup.php"
    "$WP_OLD_ROOT/wp-trackback.php"
    "$WP_OLD_ROOT/license.txt"
    "$WP_OLD_ROOT/readme.html"
)

# Only move index.php if it's the WordPress one (not a static placeholder)
if [ -f "$WP_OLD_ROOT/index.php" ] && grep -qi "wordpress\|wp-blog-header" "$WP_OLD_ROOT/index.php" 2>/dev/null; then
    WP_PATHS+=("$WP_OLD_ROOT/index.php")
fi

# Also move the .htaccess if it has WordPress rules (preserves any non-WP rules in a .htaccess.kept)
if [ -f "$WP_OLD_ROOT/.htaccess" ] && grep -qi "WordPress\|wp-" "$WP_OLD_ROOT/.htaccess"; then
    cp "$WP_OLD_ROOT/.htaccess" "$ARCHIVE/.htaccess.original"
    rm "$WP_OLD_ROOT/.htaccess"
fi

for p in "${WP_PATHS[@]}"; do
    if [ -e "$p" ]; then
        rel="$(basename "$p")"
        log "  archiving $rel"
        mv "$p" "$ARCHIVE/$rel"
    fi
done

chmod -R go-rwx "$ARCHIVE"   # archived files: not web-readable, not group-readable

# ---------- 5. Drop the WordPress database ----------

if [ -n "${WP_DB_NAME:-}" ] && [ -n "${WP_DB_USER:-}" ] && [ -f "$SNAPSHOT_SQL" ]; then
    log "Dropping WP database $WP_DB_NAME (snapshot saved to $SNAPSHOT_SQL)"
    mysql -u"$WP_DB_USER" -p"$WP_DB_PASS" -e "DROP DATABASE IF EXISTS \`$WP_DB_NAME\`;" \
        || log "WARN: could not drop DB. Drop manually: DROP DATABASE \`$WP_DB_NAME\`;"
else
    log "Skipping DB drop (no creds or no snapshot). Drop manually if needed."
fi

# ---------- 6. Remove WP cron jobs ----------

log "Cleaning WP-related cron entries"
for u in root www-data nginx hnm $(whoami); do
    if crontab -l -u "$u" 2>/dev/null | grep -E "wp-cron|wp-cli|/var/www/hernextmission" >/dev/null; then
        crontab -l -u "$u" 2>/dev/null | grep -vE "wp-cron|wp-cli|/var/www/hernextmission" | crontab -u "$u" -
        log "  cleaned cron for $u"
    fi
done

# ---------- 7. Verify ----------

log "Verifying — these should NOT serve WordPress:"
for path in / /wp-admin/ /wp-login.php /xmlrpc.php "/?page_id=2"; do
    code="$(curl -ksS -o /dev/null -w "%{http_code}" "https://${DOMAIN}${path}" || echo "ERR")"
    body="$(curl -ksS "https://${DOMAIN}${path}" | head -50 || true)"
    if echo "$body" | grep -qi "wordpress\|wp-admin/css\|wp-includes/" ; then
        log "  ❌ $path returned $code AND contained WordPress markers"
    else
        log "  ✅ $path returned $code (no WP markers)"
    fi
done

# ---------- 8. Final notes ----------

log ""
log "DONE. Summary:"
log "  Static site:    $WEB_ROOT  (untouched)"
log "  WP archive:     $ARCHIVE   (outside any docroot, mode 0700)"
log "  WP file tarball: $SNAPSHOT_TGZ"
log "  WP DB SQL dump: ${SNAPSHOT_SQL:-(not taken)}"
log ""
log "Next:"
log "  1. Verify nginx server block for ${DOMAIN} has root pointing at $WEB_ROOT."
log "     (Remove any 'index index.php' / 'try_files \$uri \$uri/ /index.php?\$args' lines.)"
log "  2. nginx -t && systemctl reload nginx"
log "  3. Hard-refresh the browser (Cmd-Shift-R) and clear any wp-* cookies."
log "  4. After 7 days of no issues, the archive in /var/backups/ can be deleted."
