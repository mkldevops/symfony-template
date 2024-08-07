<?php

namespace project;

use Castor\Attribute\AsTask;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

use function Castor\io;
use function Castor\run;
use function git\commit;

#[AsTask(description: 'Init symfony project')]
function init(bool $overwrite = false, bool $gitpod = false, bool $commit = false): void
{
    clean($overwrite);

    create();

    composer();

    if ($commit) {
        commit(message: 'Init project', noRebase: true);
    }

    makes();

    template(gitpod: $gitpod);

    up(restart: true, build: true, removeVolumes: true);
}

#[AsTask(description: 'Apply makes')]
function makes(): void
{
    io()->title('Apply makes');

    run('git checkout .');

    if (!file_exists(getWorkDir() . '/src/Entity/User.php')) {
        $answer = io()->ask('Do you want to create a user entity ? (y/n)', 'n');
        if (str_starts_with(strtolower($answer), 'y')) {
            run(['symfony', 'console', 'make:user', '--is-entity', '--no-interaction', '--with-password', '--with-uuid', '--identity-property-name', 'email', 'User']);
        }
    }

    if (!file_exists(getWorkDir() . '/src/Controller/HomeController.php')) {
        $answer = io()->ask('Do you want to create a home controller ? (y/n)', 'n');
        if (str_starts_with(strtolower($answer), 'y')) {
            run(['symfony', 'console', 'make:controller', 'home', '--invokable']);
        }
    }

    if (!file_exists(getWorkDir() . '/src/Controller/SecurityController.php')) {
        $answer = io()->ask('Do you want to create a form login ? (y/n)', 'n');
        if (str_starts_with(strtolower($answer), 'y')) {
            run(['symfony', 'console', 'make:security:form-login']);
        }
    }
}

#[AsTask(description: 'Apply template')]
function template(bool $gitpod = false): void
{
    io()->title('Apply template...');
    $finder = (new Finder())
        ->in(getWorkDir() . '/.template/symfony')
        ->ignoreDotFiles(false)
        ->depth(0);

    foreach ($finder as $item) {
        run(['cp', '-r', $item, './']);
    }

    if (!$gitpod) {
        return;
    }

    $finder = (new Finder())
        ->in(getWorkDir() . '/.template/gitpod')
        ->ignoreDotFiles(false)
        ->depth(0);

    foreach ($finder as $item) {
        run(['cp', '-r', $item, './']);
    }
}

/**
 * @return literal-string
 */
function getWorkDir(): string
{
    return __DIR__ . '/..';
}

#[AsTask(description: 'Clean symfony project')]
function clean(bool $overwrite = true): void
{
    $finder = (new Finder())
        ->in(getWorkDir())
        ->depth(0)
        ->ignoreDotFiles(false)
        ->exclude(['.castor', '.git', '.github', '.template']);

    if (!$finder->hasResults()) {
        io()->text('Project not initialized ' . getWorkDir());
        return;
    }

    io()->error('Project already initialized');
    io()->title('Cleaning project...');

    if (!$overwrite) {
        $result = io()->ask('Do you want to continue ? (y/n)', 'n');
        if (!str_starts_with(strtolower($result), 'y')) {
            exit();
        }
    }

    foreach ($finder as $item) {
        if (str_contains($item->getRealPath(), 'Makefile')
            || str_contains($item->getRealPath(), 'README.md')) {
            continue;
        }

        io()->text('Removing ' . $item->getRealPath());
        run(['rm', '-rf', $item->getRealPath()]);
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

    file_put_contents('.gitignore', implode(PHP_EOL, [
        '.idea',
        '.vscode',
    ]), FILE_APPEND);
}

function composer(): void
{
    io()->title('Installing composer dependencies...');

    $require = fn(array $packages, bool $dev = false): Process => run(
        command: [
            ...['symfony', 'composer', 'require', '-n', '-W', '--no-progress'],
            ...$dev ? ['--dev'] : [],
            ...$packages,
        ],
        quiet: true,
        callback: fn() => io()->write('>')
    );

    $require(['stof/doctrine-extensions-bundle']);


    $answer = io()->ask('Do you want to install easy admin ? (y/n,p)', 'n');
    $permanent = $answer === 'p';
    if (str_starts_with(strtolower($answer), 'y') || str_starts_with(strtolower($answer), 'p')) {
        $require(['admin']);
    }

    $answer = $permanent ?: io()->ask('Do you want to install api platform ? (y/n)', 'n');
    if (str_starts_with(strtolower($answer), 'y')) {
        $require(['api', 'webonyx/graphql-php']);
    }

    $answer = $permanent ?: io()->ask('Do you want to install phpstan ? (y/n)', 'n');
    if (str_starts_with(strtolower($answer), 'y')) {
        run(['composer', 'config', '--no-plugins', 'allow-plugins.phpstan/extension-installer', 'true'], quiet: true);
        $require(['phpstan/extension-installer',
            'phpstan/phpstan',
            'phpstan/phpstan-deprecation-rules',
            'phpstan/phpstan-doctrine',
            'phpstan/phpstan-phpunit',
            'phpstan/phpstan-strict-rules',
            'phpstan/phpstan-symfony',
            'phpstan/phpstan-webmozart-assert']);
    }

    $answer = $permanent ?: io()->ask('Do you want to install php-cs-fixer ? (y/n)', 'n');
    if (str_starts_with(strtolower($answer), 'y')) {
        $require(['friendsofphp/php-cs-fixer']);
    }

    $answer = $permanent ?: io()->ask('Do you want to install phpunit ? (y/n)', 'n');
    if (str_starts_with(strtolower($answer), 'y')) {
        $require(['phpunit/phpunit', 'dama/doctrine-test-bundle', 'doctrine/doctrine-fixtures-bundle', 'fakerphp/faker']);
    }

    $answer = $permanent ?: io()->ask('Do you want to install rector ? (y/n)', 'n');
    if (str_starts_with(strtolower($answer), 'y')) {
        $require(['rector/rector']);
    }
}
