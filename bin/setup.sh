#!/usr/bin/env bash
# ローカル WordPress の初期セットアップ（初回のみ）。
set -euo pipefail
cd "$(dirname "$0")/.."

until docker compose exec -T wordpress test -f /var/www/html/wp-config.php; do sleep 2; done

docker compose run --rm -T --entrypoint sh cli -c '
set -e
wp core is-installed 2>/dev/null || wp core install --url=http://localhost:8080 --title="青木工業株式会社" \
	--admin_user=admin --admin_password=admin --admin_email=admin@example.com --skip-email
wp language core install ja --activate || true
wp option update timezone_string Asia/Tokyo
wp option update blogdescription "自動ドア・ステンレスサッシ"
wp rewrite structure "/%postname%/"
wp theme activate aoki
wp rewrite flush
wp post list --post_type=page --fields=ID,post_name,post_parent
wp post list --post_type=works --format=count
'
echo "Ready: http://localhost:8080/"
