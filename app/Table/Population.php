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

namespace PSX\Framework\App\Table;

use PSX\Sql\Condition;
use PSX\Sql\Sql;
use PSX\Sql\TableAbstract;

/**
 * Population
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Population extends TableAbstract
{
    public function getName()
    {
        return 'population';
    }

    public function getColumns()
    {
        return array(
            'id'          => self::TYPE_INT | 10 | self::AUTO_INCREMENT | self ::PRIMARY_KEY,
            'place'       => self::TYPE_INT | 10,
            'region'      => self::TYPE_VARCHAR | 64,
            'population'  => self::TYPE_INT | 10,
            'users'       => self::TYPE_INT | 10,
            'world_users' => self::TYPE_FLOAT,
            'insert_date' => self::TYPE_DATETIME,
        );
    }

    public function getPopulations(int $startIndex = null, int $count = null)
    {
        if (empty($startIndex) || $startIndex < 0) {
            $startIndex = 0;
        }

        if (empty($count) || $count < 1 || $count > 1024) {
            $count = 16;
        }

        $condition = new Condition();

        $definition = [
            'totalResults' => $this->getCount($condition),
            'startIndex' => $startIndex,
            'itemsPerPage' => $count,
            'entry' => $this->doCollection([$this, 'getAll'], [$startIndex, $count, 'priority', Sql::SORT_DESC, $condition], [
                'id' => $this->fieldInteger('id'),
                'place' => $this->fieldInteger('place'),
                'region' => 'region',
                'population' => $this->fieldInteger('population'),
                'users' => $this->fieldInteger('users'),
                'worldUsers' => $this->fieldInteger('world_users'),
                'datetime' => $this->fieldDateTime('insert_date'),
            ]),
        ];

        return $this->build($definition);
    }
}
