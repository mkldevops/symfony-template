<?php

namespace symfony;

use Symfony\Component\Process\Process;
use function docker\exec;
use function Castor\run;
use Castor\Attribute\AsTask;
use function Castor\capture;
use function Castor\io;
use function Symfony\Component\String\u;

#[AsTask(description: 'Execute symfony command')]
function console(string $cmd, string $env = 'dev', bool $silent = false): Process
{
    return exec('php bin/console '.$cmd , env: $env, silent: $silent);
}

#[AsTask(description: 'Execute symfony command')]
function doctrine(string $cmd, string $env = 'dev'): Process
{
    return console('doctrine:'.$cmd, env: $env);
}

#[AsTask(description: 'Execute symfony command')]
function cc(string $env = 'dev'): Process
{
    return console('cache:clear', env: $env);
}

#[AsTask(description: 'Execute symfony command')]
function fixtures(string $env = 'dev', ?string $group = null, bool $append = false, bool $silent = false): Process
{
    if(capture('php bin/console debug:container doctrine.fixtures.loader')) {
        $cmd = 'php bin/console doctrine:fixtures:load --no-interaction '
            .($group !== null && $group !== '' && $group !== '0' ? '--group='.$group : '')
            .($append ? ' --append' : '');

        return exec(
            cmd: $cmd,
            env: $env,
            silent: $silent
        );
    }

    io()->error('DoctrineFixturesBundle not installed');
}

#[AsTask(description: 'Apply previous migration')]
function previousMigration(): bool
{
    $last = capture('docker compose exec php php bin/console doctrine:migrations:current');
    [$last] = explode(' - ', $last);
    io()->info('Last migration : '.$last);

    $prev = doctrine('migration:migrate prev -n');
    if (!$prev->isSuccessful()) {
        io()->error('Error while migrating');
        return false;
    }

    $file = sprintf(__DIR__."/../migrations/%s.php", u($last)->afterLast('\\'));
    if(!file_exists($file)) {
        io()->error(sprintf('Migration file "%s" not found', $file));
        return false;
    }

    run(sprintf("rm -f %s", $file));
    io()->info('Migration reverted and removed');
    return true;
}

#[AsTask(description: 'Execute symfony command')]
function migration(bool $amend = false): bool
{
    if ($amend && !previousMigration()) {
        return false;
    }

    console('make:migration --formatted');
    doctrine('migration:migrate --no-interaction');

    return true;
}
