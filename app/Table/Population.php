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

namespace PSX\Framework\App\Table;

use PSX\Framework\App\Table\Generated\PopulationTable;
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
class Population extends PopulationTable
{
    public function getCollection(?int $startIndex = null, ?int $count = null)
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
            'entry' => $this->doCollection([$this, 'findAll'], [$condition, $startIndex, $count, 'priority', Sql::SORT_DESC], [
                'id' => $this->fieldInteger('id'),
                'place' => $this->fieldInteger('place'),
                'region' => 'region',
                'population' => $this->fieldInteger('population'),
                'users' => $this->fieldInteger('users'),
                'worldUsers' => $this->fieldNumber('world_users'),
                'datetime' => $this->fieldDateTime('insert_date'),
            ]),
        ];

        return $this->build($definition);
    }

    public function getEntity(int $id)
    {
        $definition = $this->doEntity([$this, 'find'], [$id], [
            'id' => $this->fieldInteger('id'),
            'place' => $this->fieldInteger('place'),
            'region' => 'region',
            'population' => $this->fieldInteger('population'),
            'users' => $this->fieldInteger('users'),
            'worldUsers' => $this->fieldNumber('world_users'),
            'datetime' => $this->fieldDateTime('insert_date'),
        ]);

        return $this->build($definition);
    }
}
