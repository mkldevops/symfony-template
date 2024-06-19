<?php

use Castor\Attribute\AsContext;
use Castor\Attribute\AsTask;
use Castor\Context;

use function app\configEnv;
use function app\fixtures;
use function Castor\import;
use function Castor\io;
use function Castor\load_dot_env;
use function Castor\log;
use function Castor\run;
use function docker\isContainerHealthy;
use function docker\isContainerRunning;
use function git\autoCommit;
use function git\push as gitPush;
use function git\rebase;
use function project\init;
use function quality\analyze;
use function symfony\console;
use function docker\exec as dockerExec;
use function docker\up as dockerUp;
use function test\all as testAll;

import(__DIR__);

#[AsContext]
function myContext(): Context
{
    if (!file_exists('.env')) {
        return new Context();
    }

    log('Loading context');
    return new Context(load_dot_env());
}

#[AsTask(description: 'Init symfony project')]
function initProject(bool $overwrite = false): void
{
    init($overwrite);
}

#[AsTask(description: 'Install project')]
function install(): void
{
    io()->title('Installing project');

    configEnv();
    sync(dropDatabase: false, fixture: true);
    dockerUp(build: true);

    io()->success('Project installed');
}

#[AsTask(description: 'Install project')]
function sync(bool $dropDatabase = true, bool $fixture = false): void
{
    io()->title('Syncing project');
    if (!isContainerHealthy() || !isContainerRunning()) {
        io()->warning('Container is not healthy');
        dockerUp(restart: true, removeVolumes: true);
    }

    $progress = io()->createProgressBar(5);
    dockerExec('composer install', silent: true);
    $progress->advance();
    dockerExec('npm install', silent: true);
    $progress->advance();
    dockerExec('npm run dev', silent: true);
    $progress->advance();

    if ($dropDatabase) {
        console('doctrine:database:drop --force --if-exists', silent: true);
    }

    console('doctrine:database:create --if-not-exists', silent: true);

    $progress->advance();
    console('doctrine:migrations:migrate --no-interaction', silent: true);
    $progress->advance();

    if ($fixture) {
        fixtures(silent: true);
    }

    $progress->finish();
}

#[AsTask(description: 'Git commit and push')]
function up(bool $restart = false, bool $build = false, bool $removeVolumes = false): void
{
    dockerUp(
        restart: $restart,
        build: $build,
        removeVolumes: $removeVolumes
    );
}

#[AsTask(description: 'Git commit and push')]
function push(?string $message = null, bool $noRebase = false, bool $noCheck = false): void
{
    io()->title('Committing and pushing');

    if (file_exists('bin/console') && !$noCheck) {
        analyze();
        testAll();
    }

    autoCommit($message);
    if (!$noRebase) {
        rebase();
    }

    gitPush();
}
