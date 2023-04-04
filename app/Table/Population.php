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

use PSX\Framework\App\Model;
use PSX\Framework\App\Table\Generated\PopulationRow;
use PSX\Framework\App\Table\Generated\PopulationTable;
use PSX\Sql\Condition;
use PSX\Sql\OrderBy;

/**
 * Population
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Population extends PopulationTable
{
    public function getCollection(?int $startIndex = null, ?int $count = null): Model\Collection
    {
        if (empty($startIndex) || $startIndex < 0) {
            $startIndex = 0;
        }

        if (empty($count) || $count < 1 || $count > 1024) {
            $count = 16;
        }

        $condition = Condition::withAnd();

        $entries = [];
        $result = $this->findAll($condition, $startIndex, $count, 'place', OrderBy::ASC);
        foreach ($result as $row) {
            $entries[] = $this->mapModel($row);
        }

        $collection = new Model\PopulationCollection();
        $collection->setTotalResults($this->getCount($condition));
        $collection->setEntry($entries);

        return $collection;
    }

    public function getEntity(int $id): ?Model\Population
    {
        $row = $this->find($id);
        if (!$row instanceof PopulationRow) {
            return null;
        }

        return $this->mapModel($row);
    }

    private function mapModel(PopulationRow $row): Model\Population
    {
        $entity = new Model\Population();
        $entity->setId($row->getId());
        $entity->setPlace($row->getPlace());
        $entity->setRegion($row->getRegion());
        $entity->setPopulation($row->getPopulation());
        $entity->setUsers($row->getUsers());
        $entity->setWorldUsers($row->getWorldUsers());
        $entity->setInsertDate($row->getInsertDate());
        return $entity;
    }
}
