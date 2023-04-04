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

use PSX\DateTime\LocalDateTime;
use PSX\Framework\App\Model;
use PSX\Framework\App\Table\Generated\PopulationRow;
use PSX\Framework\App\Table\Population as TablePopulation;
use PSX\Http\Exception as StatusCode;

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

    public function get(int $id): PopulationRow
    {
        $population = $this->populationTable->find($id);
        if (empty($population)) {
            throw new StatusCode\NotFoundException('Internet population not found');
        }

        return $population;
    }

    public function create(Model\Population $population): void
    {
        $row = new PopulationRow();
        $row->setPlace($population->getPlace());
        $row->setRegion($population->getRegion());
        $row->setPopulation($population->getPopulation());
        $row->setUsers($population->getUsers());
        $row->setWorldUsers($population->getWorldUsers());
        $row->setInsertDate(LocalDateTime::now());
        $this->populationTable->create($row);
    }

    public function update(int $id, Model\Population $population): void
    {
        $row = $this->get($id);
        $row->setPlace($population->getPlace());
        $row->setRegion($population->getRegion());
        $row->setPopulation($population->getPopulation());
        $row->setUsers($population->getUsers());
        $row->setWorldUsers($population->getWorldUsers());
        $this->populationTable->update($row);
    }

    public function delete(int $id): void
    {
        $row = $this->get($id);

        $this->populationTable->delete($row);
    }
}
