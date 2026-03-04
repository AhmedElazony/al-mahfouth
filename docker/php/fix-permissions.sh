#!/bin/sh
set -e

chown -R $(id -u):$(id -g) /var/www/html/storage
chown -R $(id -u):$(id -g) /var/www/html/bootstrap/cache

chmod 777 -R /var/www/html/storage
chmod 777 -R /var/www/html/bootstrap/cache

exec "$@"