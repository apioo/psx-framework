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

namespace PSX\Framework\Test\Operation;

/**
 * Custom truncate operation which cascades and restarts the identity for
 * postgres and also does not quote table names
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Truncate extends \PHPUnit_Extensions_Database_Operation_Truncate
{
    public function execute(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, \PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        $tables = [];
        foreach ($dataSet->getReverseIterator() as $table) {
            /* @var $table \PHPUnit_Extensions_Database_DataSet_ITable */
            $tables[] = $table->getTableMetaData()->getTableName();
        }

        foreach ($dataSet->getReverseIterator() as $table) {
            /* @var $table \PHPUnit_Extensions_Database_DataSet_ITable */
            $query = $this->getTruncateQuery($connection, $table);

            try {
                $this->disableForeignKeyChecksForMysql($connection);
                $connection->getConnection()->query($query);
                $this->enableForeignKeyChecksForMysql($connection);
            } catch (\Exception $e) {
                $this->enableForeignKeyChecksForMysql($connection);

                if ($e instanceof \PDOException) {
                    throw new \PHPUnit_Extensions_Database_Operation_Exception('TRUNCATE', $query, [], $table, $e->getMessage());
                }

                throw $e;
            }
        }
    }

    private function getTruncateQuery(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, \PHPUnit_Extensions_Database_DataSet_ITable $table)
    {
        $query = "{$connection->getTruncateCommand()} {$table->getTableMetaData()->getTableName()}";

        if ($this->isPostgres($connection)) {
            $query .= ' RESTART IDENTITY CASCADE';
        }

        return $query;
    }
    
    private function disableForeignKeyChecksForMysql(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection)
    {
        if ($this->isMysql($connection)) {
            $connection->getConnection()->query('SET FOREIGN_KEY_CHECKS = 0');
        }
    }

    private function enableForeignKeyChecksForMysql(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection)
    {
        if ($this->isMysql($connection)) {
            $connection->getConnection()->query('SET FOREIGN_KEY_CHECKS = 1');
        }
    }

    private function isMysql(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection)
    {
        return $connection->getConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'mysql';
    }

    private function isPostgres(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection)
    {
        return $connection->getConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'pgsql';
    }
}
