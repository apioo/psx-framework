<?php

require_once __DIR__ . '/../vendor/autoload.php';

$container = require_once __DIR__ . '/container.php';

$params = getConnectionParams();

/** @var \PSX\Framework\Test\Environment $environment */
$environment = $container->get(\PSX\Framework\Test\Environment::class);

$environment->setup($params, function (\Doctrine\DBAL\Schema\Schema $fromSchema) {
    // create the database schema if not available
    if (!$fromSchema->hasTable('psx_handler_comment')) {
        $schema = \PSX\Framework\App\TestSchema::getSchema();

        $table = $schema->createTable('psx_handler_comment');
        $table->addColumn('id', 'integer', ['length' => 10, 'autoincrement' => true]);
        $table->addColumn('userId', 'integer', ['length' => 10]);
        $table->addColumn('title', 'string', ['length' => 32]);
        $table->addColumn('date', 'datetime');
        $table->setPrimaryKey(['id']);

        $table = $schema->createTable('psx_session_handler_sql_test');
        $table->addColumn('id', 'string', ['length' => 32]);
        $table->addColumn('content', 'blob');
        $table->addColumn('date', 'datetime');
        $table->setPrimaryKey(['id']);

        return $schema;
    }
});

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
