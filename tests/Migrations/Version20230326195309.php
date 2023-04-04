<?php

declare(strict_types=1);

namespace PSX\Framework\Tests\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230326195309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
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

        $table = $schema->createTable('app_population');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('place', 'integer');
        $table->addColumn('region', 'string');
        $table->addColumn('population', 'integer');
        $table->addColumn('users', 'integer');
        $table->addColumn('world_users', 'float');
        $table->addColumn('insert_date', 'datetime');
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
