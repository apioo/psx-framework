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

namespace PSX\Framework\App\Service;

use PSX\Framework\App\Table\Generated\PopulationRow;
use PSX\Http\Exception as StatusCode;
use PSX\Model\Common\ResultSet;
use PSX\Framework\App\Table\Population as TablePopulation;

/**
 * Population
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Population
{
    private TablePopulation $populationTable;

    public function __construct(TablePopulation $populationTable)
    {
        $this->populationTable = $populationTable;
    }

    public function getAll(?int $startIndex = null, ?int $count = null)
    {
        return $this->populationTable->getCollection($startIndex, $count);
    }

    public function get(int $id)
    {
        $population = $this->populationTable->getEntity($id);

        if (empty($population)) {
            throw new StatusCode\NotFoundException('Internet population not found');
        }

        return $population;
    }

    public function create($place, $region, $count, $users, $worldUsers)
    {
        $this->populationTable->create(new PopulationRow([
            'place'       => $place,
            'region'      => $region,
            'population'  => $count,
            'users'       => $users,
            'world_users' => $worldUsers,
            'insert_date' => new \DateTime(),
        ]));
    }

    public function update($id, $place, $region, $count, $users, $worldUsers)
    {
        $population = $this->get($id);

        $this->populationTable->update(new PopulationRow([
            'id'          => $population['id'],
            'place'       => $place,
            'region'      => $region,
            'population'  => $count,
            'users'       => $users,
            'world_users' => $worldUsers,
        ]));
    }

    public function delete($id)
    {
        $population = $this->get($id);

        $this->populationTable->delete(new PopulationRow([
            'id' => $population['id']
        ]));
    }
}
