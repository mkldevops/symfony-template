#!/bin/sh
set -e

echo "Install project..."
rm -Rf tmp/

git config --global user.email "you@example.com"
git config --global user.name "Your Name"

symfony new --webapp tmp

cd tmp
rm -rf docker-compose* .git
symfony composer require "php:>=$PHP_VERSION"
symfony composer config --json extra.symfony.docker 'true'
cp -Rp . ..
cd -

rm -Rf tmp/

! test -f .env.local && echo 'DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"' > .env.local
symfony console doctrine:schema:create

symfony composer req --dev phpstan/phpstan cs-fixer rector/rector in
symfony composer req admin api webonyx/graphql-php

cp -Rp .template/* .