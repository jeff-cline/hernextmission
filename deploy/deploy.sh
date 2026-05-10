#!/usr/bin/env bash
#
# HER NEXT MISSION — static deploy.
#
# Rsyncs the site (HTML + /assets) to the web root for Nginx/Apache to
# serve. The site is now plain HTML/CSS/JS — no WordPress, no PHP runtime,
# no database. The web server only needs to point its document root at
# WEB_ROOT and the site is live.
#
# Required env:
#   WEB_ROOT       — absolute path the web server serves from.
#                    Default: /var/www/hernextmission/htdocs
#   HNM_REPO_ROOT  — absolute path to the cloned repo on the server.
#                    Default: directory containing this script's parent
#                    (so deploy/deploy.sh works invoked from the repo root).
#
# Safety:
#   - Uses `rsync -a --delete` so files removed from the repo also get
#     removed from the web root. Targets ONLY the static deliverables —
#     index.html, the other *.html pages, /assets/. Will NOT touch any
#     other directory under WEB_ROOT (so a sibling site at a parallel
#     path is unaffected).
#   - Does not require root if the user owns WEB_ROOT.

set -euo pipefail

WEB_ROOT="${WEB_ROOT:-/var/www/hernextmission/htdocs}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HNM_REPO_ROOT="${HNM_REPO_ROOT:-$(cd "$SCRIPT_DIR/.." && pwd)}"

log()  { printf '[hnm-deploy] %s\n' "$*"; }
fail() { printf '[hnm-deploy] ERROR: %s\n' "$*" >&2; exit 1; }

command -v rsync >/dev/null 2>&1 || fail "rsync not found in PATH"
[ -d "$HNM_REPO_ROOT" ] || fail "HNM_REPO_ROOT does not exist: $HNM_REPO_ROOT"
[ -f "$HNM_REPO_ROOT/index.html" ] || fail "index.html not found in $HNM_REPO_ROOT — wrong repo root?"

mkdir -p "$WEB_ROOT"

log "WEB_ROOT       = $WEB_ROOT"
log "HNM_REPO_ROOT  = $HNM_REPO_ROOT"

# ---------- sync HTML pages ----------
# Top-level *.html files only.
log "Syncing HTML pages → $WEB_ROOT"
rsync -av --include='*.html' --exclude='*' "$HNM_REPO_ROOT/" "$WEB_ROOT/" | tail -n +2

# ---------- sync /assets/ ----------
log "Syncing /assets/ → $WEB_ROOT/assets/"
mkdir -p "$WEB_ROOT/assets"
rsync -a --delete "$HNM_REPO_ROOT/assets/" "$WEB_ROOT/assets/"

log "Done. Hard-refresh the browser (Cmd-Shift-R / Ctrl-F5) to bypass cache."
