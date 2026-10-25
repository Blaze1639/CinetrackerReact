#!/bin/sh
set -e

php bin/console doctrine:migrations:migrate --no-interaction --env=prod

# Les migrations tournent en root : on rend le cache à PHP-FPM (www-data).
chown -R www-data:www-data var/

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
