#!/bin/sh
set -e

php bin/console doctrine:migrations:migrate --no-interaction --env=prod

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
