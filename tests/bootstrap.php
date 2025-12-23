<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Process\Process;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

if (($_SERVER['APP_ENV'] ?? null) === 'test') {
    $commands = [
        ['php', 'bin/console', 'doctrine:database:drop', '--if-exists', '--force', '--env=test'],
        ['php', 'bin/console', 'doctrine:database:create', '--env=test'],
        ['php', 'bin/console', 'doctrine:migrations:migrate', '--no-interaction', '--env=test'],
    ];

    foreach ($commands as $command) {
        (new Process($command))->mustRun();
    }
}
