#!/usr/bin/env bash
# Undo bin/cutover.sh by restoring the database dump it made first. The old theme and plugins
# are still on disk, so the database is all that changed.
#
# On SiteGround: bin/rollback.sh ~/backups/pre-cutover-<timestamp>.sql
# Rehearsal:     WP="docker compose -f rehearsal/docker-compose.yml run --rm -T cli wp" bin/rollback.sh /var/www/html/pre-cutover.sql
set -euo pipefail

DUMP=${1:?Usage: bin/rollback.sh <pre-cutover dump>}
WP=${WP:-wp --path=$HOME/www/djmichael.com/public_html}
wp() { command $WP "$@"; }  # "command": $WP starts with "wp" on the server; skip this function

start=$(date +%s)
wp db import "$DUMP"
wp maintenance-mode deactivate || true
wp rewrite flush
wp cache flush
wp sg purge || echo "SiteGround cache not purged; flush it in Site Tools."
echo "Rolled back in $(( $(date +%s) - start ))s"
