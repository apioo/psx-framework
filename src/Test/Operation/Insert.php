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
 * Custom insert operation which does not quote column and table identifiers
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Insert extends \PHPUnit_Extensions_Database_Operation_Insert
{
    protected function buildOperationQuery(\PHPUnit_Extensions_Database_DataSet_ITableMetaData $databaseTableMetaData, \PHPUnit_Extensions_Database_DataSet_ITable $table, \PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection)
    {
        $columnCount = count($table->getTableMetaData()->getColumns());

        if ($columnCount > 0) {
            $placeHolders = implode(', ', array_fill(0, $columnCount, '?'));

            $columns = '';
            foreach ($table->getTableMetaData()->getColumns() as $column) {
                $columns .= $column . ', ';
            }

            $columns = substr($columns, 0, -2);

            $query = "
                INSERT INTO {$table->getTableMetaData()->getTableName()}
                ({$columns})
                VALUES
                ({$placeHolders})
            ";

            return $query;
        } else {
            return FALSE;
        }
    }
}
