<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\App;

use Doctrine\DBAL\Schema\Schema;

/**
 * TestSchema
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestSchema
{
    public static function getSchema()
    {
        $schema = new Schema();

        $table = $schema->createTable('population');
        $table->addColumn('id', 'integer', array('autoincrement' => true));
        $table->addColumn('place', 'integer');
        $table->addColumn('region', 'string');
        $table->addColumn('population', 'integer');
        $table->addColumn('users', 'integer');
        $table->addColumn('world_users', 'float');
        $table->addColumn('insert_date', 'datetime');
        $table->setPrimaryKey(array('id'));

        return $schema;
    }
}
