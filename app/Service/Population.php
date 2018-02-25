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

namespace PSX\Framework\App\Service;

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
    protected $populationTable;

    public function __construct(TablePopulation $populationTable)
    {
        $this->populationTable = $populationTable;
    }

    public function getAll($startIndex = 0, $count = 16)
    {
        return new ResultSet(
            $this->populationTable->getCount(),
            $startIndex,
            $count,
            $this->populationTable->getAll($startIndex, $count)
        );
    }

    public function get($id)
    {
        $population = $this->populationTable->get($id);

        if (empty($population)) {
            throw new StatusCode\NotFoundException('Internet population not found');
        }

        return $population;
    }

    public function create($place, $region, $count, $users, $worldUsers)
    {
        $this->populationTable->create([
            'place'      => $place,
            'region'     => $region,
            'population' => $count,
            'users'      => $users,
            'worldUsers' => $worldUsers,
            'datetime'   => new \DateTime(),
        ]);
    }

    public function update($id, $place, $region, $count, $users, $worldUsers)
    {
        $population = $this->get($id);

        $this->populationTable->update([
            'id'         => $population['id'],
            'place'      => $place,
            'region'     => $region,
            'population' => $count,
            'users'      => $users,
            'worldUsers' => $worldUsers,
        ]);
    }

    public function delete($id)
    {
        $population = $this->get($id);

        $this->populationTable->delete([
            'id' => $population['id']
        ]);
    }
}
