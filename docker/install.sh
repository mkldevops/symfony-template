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

symfony composer req --dev phpstan/phpstan \
  phpstan/extension-installer \
  phpstan/phpstan-deprecation-rules  \
  phpstan/phpstan-phpunit \
  phpstan/phpstan-strict-rules \
  phpstan/phpstan-webmozart-assert \
  phpstan/phpstan-doctrine \
  phpstan/phpstan-symfony \
  cs-fixer \
  rector/rector \
  infection/infection \
  hautelook/alice-bundle \
  phpunit/phpunit \
  dama/doctrine-test-bundle;


symfony composer req admin \
  api \
  webonyx/graphql-php \
  gesdinet/jwt-refresh-token-bundle \
  lexik/jwt-authentication-bundle \
  nelmio/cors-bundle \
  sentry/sentry-symfony;

cp -Rp .template/* .