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

namespace PSX\Framework\App\Controller;

use PSX\Api\Attribute\Delete;
use PSX\Api\Attribute\Description;
use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\Post;
use PSX\Api\Attribute\Put;
use PSX\Api\Attribute\QueryParam;
use PSX\Framework\App\Model;
use PSX\Framework\App\Service\Population;
use PSX\Framework\App\Table;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Exception as StatusCode;
use PSX\Record\Record;
use PSX\Schema\Type;
use PSX\Sql\TableManagerInterface;

#[Description('Collection endpoint')]
class PopulationTypeSchema extends ControllerAbstract
{
    private Population $populationService;
    private Table\Population $populationTable;

    public function __construct(Population $populationService, TableManagerInterface $tableManager)
    {
        $this->populationService = $populationService;
        $this->populationTable = $tableManager->getTable(Table\Population::class);
    }

    #[Get]
    #[Path('/population/typeschema')]
    #[QueryParam(name: "startIndex", type: Type::INTEGER)]
    #[QueryParam(name: "count", type: Type::INTEGER)]
    #[Outgoing(code: 200, schema: __DIR__ . '/../Resource/schema/population/collection.json')]
    public function getAll(?int $startIndex, ?int $count): mixed
    {
        return $this->populationTable->getCollection(
            $startIndex,
            $count
        );
    }

    #[Post]
    #[Path('/population/typeschema')]
    #[Incoming(schema: __DIR__ . '/../Resource/schema/population/entity.json')]
    #[Outgoing(code: 201, schema: __DIR__ . '/../Resource/schema/population/message.json')]
    public function get(Record $payload): array
    {
        $model = new Model\Population();
        $model->setPlace($payload->get('place'));
        $model->setRegion($payload->get('region'));
        $model->setPopulation($payload->get('population'));
        $model->setUsers($payload->get('users'));
        $model->setWorldUsers($payload->get('worldUsers'));
        $this->populationService->create($model);

        return [
            'success' => true,
            'message' => 'Create population successful',
        ];
    }


    #[Get]
    #[Path('/population/typeschema/:id')]
    #[PathParam(name: "id", type: Type::INTEGER)]
    #[Outgoing(code: 200, schema: __DIR__ . '/../Resource/schema/population/entity.json')]
    public function create(int $id): mixed
    {
        $population = $this->populationTable->getEntity($id);
        if (empty($population)) {
            throw new StatusCode\NotFoundException('Internet population not found');
        }

        return $population;
    }

    #[Put]
    #[Path('/population/typeschema/:id')]
    #[PathParam(name: "id", type: Type::INTEGER)]
    #[Incoming(schema: __DIR__ . '/../Resource/schema/population/entity.json')]
    #[Outgoing(code: 200, schema: __DIR__ . '/../Resource/schema/population/message.json')]
    public function update(int $id, Record $payload): array
    {
        $model = new Model\Population();
        $model->setPlace($payload->get('place'));
        $model->setRegion($payload->get('region'));
        $model->setPopulation($payload->get('population'));
        $model->setUsers($payload->get('users'));
        $model->setWorldUsers($payload->get('worldUsers'));
        $this->populationService->update($id, $model);

        return [
            'success' => true,
            'message' => 'Update successful',
        ];
    }

    #[Delete]
    #[Path('/population/typeschema/:id')]
    #[PathParam(name: "id", type: Type::INTEGER)]
    #[Outgoing(code: 200, schema: __DIR__ . '/../Resource/schema/population/message.json')]
    public function delete(int $id): array
    {
        $this->populationService->delete($id);

        return [
            'success' => true,
            'message' => 'Delete successful',
        ];
    }
}
