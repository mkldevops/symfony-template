<?php

namespace app;

use Symfony\Component\Process\Process;
use Castor\Attribute\AsTask;
use function Castor\io;
use function Castor\log;
use function Castor\load_dot_env;
use function Castor\parallel;
use function symfony\fixtures as SymfonyFixtures;
use const FILE_APPEND;

#[AsTask(description: 'Configure environment variables')]
function configEnv(): void
{
    load_dot_env();

    touch('.env.local');
    file_put_contents('.env.local', "APP_ENV=dev\n");
    file_put_contents('.env.local', sprintf("APP_SECRET=%s\n", md5(time())), FILE_APPEND);
}

#[AsTask(description: 'Install project')]
function fixtures(bool $silent = false, string $env = 'dev'): void
{
    log('Migrating project');
    SymfonyFixtures(env: $env, silent: $silent);
}
