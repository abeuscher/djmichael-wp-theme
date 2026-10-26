#!/usr/bin/env bash
# Production cutover: replace the old theme and plugins with this theme, and seed its pages,
# behind maintenance mode. Rehearse it against rehearsal/load.sh first.
#
# Before running it on SiteGround, upload the theme to wp-content/themes/djmichael, ACF Pro to
# wp-content/plugins/advanced-custom-fields-pro and seed/media to ~/djm-seed-media, and run
# `wp plugin install fluentform` (installed, not active).
#
# The cutover only adds files, so undoing it is a database restore: bin/rollback.sh <dump>.
#
# On SiteGround: bin/cutover.sh
# Rehearsal:     WP="docker compose -f rehearsal/docker-compose.yml run --rm -T cli wp" MEDIA=/seed-media \
#                DUMP=/var/www/html/pre-cutover.sql bin/cutover.sh
set -euo pipefail

WP=${WP:-wp --path=$HOME/www/djmichael.com/public_html}
MEDIA=${MEDIA:-$HOME/djm-seed-media}
DUMP=${DUMP:-$HOME/backups/pre-cutover-$(date +%Y%m%d-%H%M%S).sql}
wp() { command $WP "$@"; }  # "command": $WP starts with "wp" on the server; skip this function

start=$(date +%s)
wp db export "$DUMP" --single-transaction --quick
echo "Rollback point: $DUMP"
trap 'echo "Cutover failed; the site is still in maintenance mode. Fix forward, or run: bin/rollback.sh $DUMP" >&2' ERR

wp maintenance-mode activate

# Live stores http:// URLs behind an https redirect. Before the seed, so its links come out https.
wp search-replace 'http://djmichael.com' 'https://djmichael.com' --all-tables-with-prefix --precise --report-changed-only

# Classic Editor would hide the block editor these pages are built in.
wp plugin deactivate wordpress-seo contact-form-7 contact-form-7-simple-recaptcha advanced-custom-fields classic-editor
wp plugin activate advanced-custom-fields-pro fluentform
wp theme activate djmichael
wp djm seed --media="$MEDIA"

# The old home page; the seed's "home" page replaces it as the front page.
old_home=$(wp post list --post_type=page --name=dj-michael-boston --field=ID)
[ -n "$old_home" ] && wp post delete "$old_home"

wp rewrite flush
wp cache flush
wp sg purge || echo "SiteGround cache not purged; flush it in Site Tools."

wp maintenance-mode deactivate
echo "Cutover done in $(( $(date +%s) - start ))s. Rollback point: $DUMP"
