#!/usr/bin/env bash
# First-time (and repeatable) local setup: start containers, install WordPress,
# activate plugins and the theme, then seed content. Safe to rerun.
set -euo pipefail
cd "$(dirname "$0")/.."

[ -f .env ] && set -a && . ./.env && set +a
PORT="${WP_PORT:-8080}"

if [ ! -f plugins/advanced-custom-fields-pro/acf.php ]; then
  echo "ACF Pro missing: unzip it so plugins/advanced-custom-fields-pro/acf.php exists, then rerun." >&2
  exit 1
fi

docker compose up -d
wp() { docker compose run --rm -T cli wp "$@"; }

echo "Waiting for WordPress..."
until docker compose exec -T wordpress test -f /var/www/html/wp-config.php 2>/dev/null && wp db check >/dev/null 2>&1; do
  sleep 2
done

if ! wp core is-installed 2>/dev/null; then
  wp core install --url="http://localhost:${PORT}" --title="DJ Michael" \
    --admin_user=admin --admin_password=admin --admin_email=dev@example.test --skip-email
fi

wp plugin activate advanced-custom-fields-pro
wp plugin is-installed fluentform || wp plugin install fluentform
wp plugin activate fluentform
wp plugin delete hello akismet >/dev/null 2>&1 || true
wp theme activate djmichael
wp djm seed

echo
echo "Site:   http://localhost:${PORT}  (admin / admin)"
echo "Mail:   http://localhost:${MAIL_PORT:-8025}"
