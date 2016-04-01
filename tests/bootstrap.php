<?php

require_once __DIR__ . '/../vendor/autoload.php';

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(function ($class) {
    spl_autoload_call($class);
    return class_exists($class, false);
});

\PSX\Framework\Test\Environment::setup(__DIR__, function(\Doctrine\DBAL\Schema\Schema $fromSchema){
    // create the database schema if not available
    if (!$fromSchema->hasTable('psx_handler_comment')) {
        $schema = new \Doctrine\DBAL\Schema\Schema();
        $table = $schema->createTable('psx_handler_comment');
        $table->addColumn('id', 'integer', array('length' => 10, 'autoincrement' => true));
        $table->addColumn('userId', 'integer', array('length' => 10));
        $table->addColumn('title', 'string', array('length' => 32));
        $table->addColumn('date', 'datetime');
        $table->setPrimaryKey(array('id'));

        $table = $schema->createTable('psx_sql_table_test');
        $table->addColumn('id', 'integer', array('length' => 10, 'autoincrement' => true));
        $table->addColumn('title', 'string', array('length' => 32));
        $table->addColumn('date', 'datetime');
        $table->setPrimaryKey(array('id'));

        $table = $schema->createTable('psx_table_command_test');
        $table->addColumn('id', 'integer', array('length' => 10, 'autoincrement' => true));
        $table->addColumn('col_bigint', 'bigint');
        $table->addColumn('col_blob', 'blob');
        $table->addColumn('col_boolean', 'boolean');
        $table->addColumn('col_datetime', 'datetime');
        $table->addColumn('col_datetimetz', 'datetimetz');
        $table->addColumn('col_date', 'date');
        $table->addColumn('col_decimal', 'decimal');
        $table->addColumn('col_float', 'float');
        $table->addColumn('col_integer', 'integer');
        $table->addColumn('col_smallint', 'smallint');
        $table->addColumn('col_text', 'text');
        $table->addColumn('col_time', 'time');
        $table->addColumn('col_string', 'string');
        $table->addColumn('col_array', 'text', array('notnull' => false));
        $table->addColumn('col_object', 'text', array('notnull' => false));
        $table->setPrimaryKey(array('id'));

        $table = $schema->createTable('psx_session_handler_sql_test');
        $table->addColumn('id', 'string', array('length' => 32));
        $table->addColumn('content', 'blob');
        $table->addColumn('date', 'datetime');
        $table->setPrimaryKey(array('id'));

        return $schema;
    }
});

