<?php

require_once __DIR__ . '/../vendor/autoload.php';

$container = require_once __DIR__ . '/container.php';

/** @var \PSX\Framework\Test\Environment $environment */
$environment = $container->get(\PSX\Framework\Test\Environment::class);
$environment->setup(getConnectionParams());

function getConnectionParams(): array
{
    switch (getenv('DB')) {
        case 'mysql':
            return [
                'dbname' => 'psx',
                'user' => 'root',
                'password' => 'test1234',
                'host' => 'localhost',
                'driver' => 'pdo_mysql',
            ];

        case 'pgsql':
            return [
                'dbname' => 'psx',
                'user' => 'postgres',
                'password' => 'test1234',
                'host' => 'localhost',
                'driver' => 'pdo_pgsql',
            ];

        default:
        case 'sqlite':
            return [
                'memory' => true,
                'driver' => 'pdo_sqlite',
            ];
    }
}
