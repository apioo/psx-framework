<?php

require_once __DIR__ . '/../vendor/autoload.php';

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(function ($class) {
    spl_autoload_call($class);
    return class_exists($class, false);
});

\PSX\Framework\Test\Environment::setup(__DIR__, function(\Doctrine\DBAL\Schema\Schema $fromSchema){
    // create the database schema if not available
    if (!$fromSchema->hasTable('psx_handler_comment')) {
        $schema = \PSX\Framework\App\TestSchema::getSchema();

        $table = $schema->createTable('psx_handler_comment');
        $table->addColumn('id', 'integer', array('length' => 10, 'autoincrement' => true));
        $table->addColumn('userId', 'integer', array('length' => 10));
        $table->addColumn('title', 'string', array('length' => 32));
        $table->addColumn('date', 'datetime');
        $table->setPrimaryKey(array('id'));

        $table = $schema->createTable('psx_session_handler_sql_test');
        $table->addColumn('id', 'string', array('length' => 32));
        $table->addColumn('content', 'blob');
        $table->addColumn('date', 'datetime');
        $table->setPrimaryKey(array('id'));

        return $schema;
    }
});
