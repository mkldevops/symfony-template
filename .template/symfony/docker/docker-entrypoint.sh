#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ ! -f composer.json ]; then
	sh ./docker/install.sh
fi

if [ ! -d vendor ]; then
	echo "Install composer..."
    symfony composer install
fi

chmod -R 777 ./

symfony console doctrine:migration:migrate -n --allow-no-migration

exec docker-php-entrypoint "$@"
