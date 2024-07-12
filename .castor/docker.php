<?php

namespace docker;

use Castor\Attribute\AsTask;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Process\Process;

use function Castor\cache;
use function Castor\capture;
use function Castor\io;
use function Castor\run;
use function Castor\variable;

#[AsTask(description: 'Install project')]
function sh(): void
{
    if (!isContainerRunning()) {
        io()->error('Container is not running');

        $restart = io()->ask('Do you want to start the container?', 'yes');
        if ('yes' !== $restart) {
            return;
        }

        up();
    }

    exec('zsh');
}

function isContainerRunning(): bool
{
    return cache('docker-is-running', static function (CacheItemInterface $item): bool {
        $item->expiresAfter(20);
        return (bool)capture('docker compose ps --status=running --services | grep php && echo 1 || echo 0');
    });
}

function isContainerHealthy(): bool
{
    return cache('docker-is-healthy', static function (CacheItemInterface $item): bool {
        $item->expiresAfter(20);
        if (isContainerRunning()) {
            return true;
        }

        $serviceUnhealthy = capture('docker compose ps --all --status=restarting --status=exited --status=dead --services');

        return $serviceUnhealthy === '';
    });
}

#[AsTask(description: 'Execute docker exec command')]
function exec(string $cmd, string $service = 'web', string $env = 'dev', bool $silent = false): Process
{
    $environment = ['APP_ENV' => $env];
    if (!isContainerRunning()) {
        io()->warning('Container is not running');
        return run($cmd, environment: $environment);
    }

    array_walk(
        array: $environment,
        callback: static fn(string &$value, string $key): string => $value = sprintf('-e %s=%s', $key, $value),
    );

    return run(
        command: strtr('docker compose exec :environment :service :cmd', [
            ':environment' => implode(' ', $environment),
            ':service' => $service,
            ':cmd' => $cmd,
        ]),
        quiet: $silent
    );
}

#[AsTask(description: 'Up project')]
function up(bool $restart = false, bool $build = false, bool $removeVolumes = false): void
{
    if ($restart) {
        down(removeVolumes: $removeVolumes);
    }

    io()->title('Starting project');

    $up = run(
        command: sprintf(
            'docker compose up -d --wait --remove-orphans  %s',
            $build ? '--build' : ''
        ),
        environment: ['SERVER_NAME' => variable('SERVER_NAME', dirname(__DIR__) . '.localhost')],
        allowFailure: true
    );

    if (!$up->isSuccessful()) {
        run('docker compose logs -f');
    }
}

#[AsTask(description: 'Down project')]
function down(bool $removeVolumes = false): void
{
    io()->title('Stopping project');

    run('docker compose down --remove-orphans ' . ($removeVolumes ? '--volumes' : ''));
}

#[AsTask(description: 'Execute docker push command')]
function push(string $target, ?string $tag = null): Process
{
    $login = run('docker login --username $DOCKER_USERNAME --password $DOCKERHUB_TOKEN');
    if (!$login->isSuccessful()) {
        io()->error('Login failed');

        return $login;
    }

    // docker build with target
    $build = run('docker build --target ' . $target . ' -t $DOCKER_IMAGE_NAME:' . $tag . ' .');

    if (!$build->isSuccessful()) {
        io()->error('Build failed');

        return $build;
    }

    $result = run('docker push $DOCKER_IMAGE_NAME:' . $tag);

    if ($result->isSuccessful()) {
        io()->success('Push executed successfully');
    }

    return $result;
}
