#!/usr/bin/env bash
# Load a SiteGround backup into the rehearsal stack, wiping the previous rehearsal.
# Usage: rehearsal/load.sh [backup dir]   (default: the newest folder in ~/djmichael-backups)
# The folder needs djm-db.sql.gz and djm-files.tar.gz, as made by the backup commands.
set -euo pipefail
cd "$(dirname "$0")"

BACKUP_DIR=$(realpath "${1:-$(ls -d ~/djmichael-backups/*/ | sort | tail -1)}")
export BACKUP_DIR
echo "Loading $BACKUP_DIR"

wp() { docker compose run --rm -T cli wp "$@"; }

docker compose down -v
docker compose up -d

echo "Waiting for WordPress..."
until docker compose exec -T wordpress test -f /var/www/html/wp-config.php 2>/dev/null && wp db check >/dev/null 2>&1; do
  sleep 2
done

# Same core version as live.
version=$(tar -xzOf "$BACKUP_DIR/djm-files.tar.gz" public_html/wp-includes/version.php | sed -n "s/^\$wp_version = '\(.*\)';/\1/p")
wp core download --version="$version" --force --skip-content

# Live's wp-content and .htaccess, without its backup folders. wp-config.php stays local.
# Extracted as www-data: a recursive chown would reach through the bind mounts into the repo.
docker compose exec -T wordpress chown www-data:www-data /var/www/html/wp-content/mu-plugins
docker compose exec -T -u www-data wordpress \
  tar -xzf /backup/djm-files.tar.gz -C /var/www/html --strip-components=1 --no-same-owner \
    --exclude="public_html/wp-content/updraft" --exclude="public_html/wp-content/ai1wm-backups" \
    --exclude="public_html/wp-content/upgrade*" \
    public_html/wp-content public_html/.htaccess

# Live runs MySQL 8.4; MariaDB doesn't know its 0900 collation.
gunzip -c "$BACKUP_DIR/djm-db.sql.gz" | sed 's/utf8mb4_0900_ai_ci/utf8mb4_unicode_520_ci/g' \
  | docker compose exec -T db mariadb -uwordpress -pwordpress wordpress

wp search-replace '//djmichael.com' '//localhost:8091' --all-tables-with-prefix --precise --report-changed-only
wp search-replace '//www.djmichael.com' '//localhost:8091' --all-tables-with-prefix --precise --report-changed-only
wp search-replace 'https://localhost:8091' 'http://localhost:8091' --all-tables-with-prefix --precise --report-changed-only

wp user create rehearsal rehearsal@example.test --role=administrator --user_pass=rehearsal

# The pre-cutover step on SiteGround: Fluent Forms installed but not active.
wp plugin install fluentform

echo
echo "Live copy: http://localhost:8091  (rehearsal / rehearsal)"
echo "Mail:      http://localhost:8036"
echo "Cutover:   WP=\"docker compose -f rehearsal/docker-compose.yml run --rm -T cli wp\" MEDIA=/seed-media bin/cutover.sh"
