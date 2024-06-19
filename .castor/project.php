<?php

namespace project;


use Castor\Attribute\AsTask;
use Symfony\Component\Finder\Finder;
use function Castor\run;
use function Castor\io;

#[AsTask(description: 'Init symfony project')]
function init(bool $overwrite = false, bool $gitpod = false): void
{
    clean($overwrite);

    create();

    composer();

    makes();

    template($gitpod);
}


function makes(): void
{
    run(['symfony', 'console', 'make:user', '--is-entity', '--no-interaction', '--with-password', '--with-uuid', '--identity-property-name==email', 'User']);
}

function template(bool $gitpod): void
{
    run(['cp', '-r', '.template/symfony', '.']);

    if ($gitpod) {
        run(['cp', '-r', '.template/gitpod', '.']);
    }
}

#[AsTask(description: 'Clean symfony project')]
function clean(bool $overwrite = true): void
{
    $finder = (new Finder())
        ->in(__DIR__ . '/..')
        ->depth(0)
        ->ignoreDotFiles(false)
        ->exclude(['.castor', '.git', '.github', '.template']);

    if ($finder->hasResults()) {
        io()->error('Project already initialized');
        io()->title('Cleaning project...');
        if (!$overwrite) {
            $result = io()->ask('Do you want to continue ? (y/n)', 'n');
            if (!str_starts_with(strtolower($result), 'y')) {
                return;
            }
        }

        foreach ($finder as $item) {
            run(['rm', '-rf', $item->getRealPath()]);
        }
    }
}

function create(): void
{
    io()->title('Creating project...');
    run(['symfony', 'new', '--dir=tmp', '--webapp'], quiet: true);
    run(['rm', '-rf', 'tmp/.git']);

    $finder = (new Finder())
        ->in('tmp')
        ->exclude(['.git'])
        ->ignoreDotFiles(false)
        ->depth(0);

    foreach ($finder as $item) {
        run(['mv', $item->getRealPath(), './']);
    }

    run(['rm', '-rf', 'tmp']);
}

function composer(): void
{
    io()->title('Installing composer dependencies...');

    run(['symfony', 'composer', 'require', '-n', '-W', '--no-progress',
        'admin',
        'api',
        'webonyx/graphql-php',
        'stof/doctrine-extensions-bundle'
    ], quiet: true);

    run(['composer', 'config', '--no-plugins', 'allow-plugins.phpstan/extension-installer', 'true'], quiet: true);
    run(['symfony', 'composer', 'require', '--dev', '-n', '-W', '--no-progress',
        'phpstan/phpstan',
        "dama/doctrine-test-bundle",
        "doctrine/doctrine-fixtures-bundle",
        "fakerphp/faker",
        "friendsofphp/php-cs-fixer",
        "phpstan/extension-installer",
        "phpstan/phpstan",
        "phpstan/phpstan-deprecation-rules",
        "phpstan/phpstan-doctrine",
        "phpstan/phpstan-phpunit",
        "phpstan/phpstan-strict-rules",
        "phpstan/phpstan-symfony",
        "phpstan/phpstan-webmozart-assert",
        "phpunit/phpunit",
        "rector/rector"
    ], quiet: true);
}