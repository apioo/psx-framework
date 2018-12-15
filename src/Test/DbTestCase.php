<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Framework\Test;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use PHPUnit\Framework\TestCase;

/**
 * Base test class for database test cases
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class DbTestCase extends TestCase
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected static $con;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @internal
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        if (!Environment::hasConnection()) {
            $this->markTestSkipped('Database connection not available');
        }

        if (self::$con === null) {
            self::$con = Environment::getService('connection');
        }

        if ($this->connection === null) {
            $this->connection = self::$con;
        }

        return $this->connection;
    }

    protected function setUp()
    {
        parent::setup();

        $this->truncate();
        $this->insert();
    }

    /**
     * @param string $file
     * @return array
     */
    protected function createFromFile($file)
    {
        return include $file;
    }

    /**
     * @return \ArrayObject|array
     */
    abstract protected function getDataSet();

    private function truncate()
    {
        $connection = $this->getConnection();
        $tables     = $connection->getSchemaManager()->listTableNames();
        $platform   = $connection->getDatabasePlatform();
        
        if ($platform instanceof MySqlPlatform) {
            $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0');

            foreach ($tables as $table) {
                $this->connection->executeQuery('TRUNCATE ' . $table);
            }

            $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1');
        } elseif ($platform instanceof PostgreSqlPlatform) {
            foreach ($tables as $table) {
                $this->connection->executeQuery('TRUNCATE ' . $table . ' RESTART IDENTITY CASCADE');
            }
        } else {
            // for all other platforms we simply try to delete all data using
            // standard SQL ignoring potential foreign key problems
            foreach ($tables as $table) {
                $this->connection->executeQuery('DELETE FROM ' . $table . ' WHERE 1=1');
            }
        }
    }

    private function insert()
    {
        // since we have moved away from DbUnit this allows old packages to
        // still use this class which now maps to our own implementation
        if (!class_exists('PHPUnit_Extensions_Database_DataSet_ArrayDataSet')) {
            class_alias(ArrayDataSet::class, 'PHPUnit_Extensions_Database_DataSet_ArrayDataSet');
        }

        $connection = $this->getConnection();
        $data       = $this->getDataSet();

        foreach ($data as $tableName => $rows) {
            foreach ($rows as $row) {
                $connection->insert($tableName, $row);
            }
        }
    }
}
