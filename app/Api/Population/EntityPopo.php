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

namespace PSX\Framework\App\Api\Population;

use PSX\Api\Attribute\Delete;
use PSX\Api\Attribute\Description;
use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\Put;
use PSX\Framework\App\Model;
use PSX\Framework\App\Service\Population;
use PSX\Framework\Controller\ControllerAbstract;

#[Description('Entity endpoint')]
#[Path('/population/popo/:id')]
#[PathParam(name: "id", type: "integer")]
class EntityPopo extends ControllerAbstract
{
    private Population $populationService;

    public function __construct(Population $populationService)
    {
        $this->populationService = $populationService;
    }

    #[Get]
    #[Outgoing(code: 200, schema: Model\Entity::class)]
    public function doGet(int $id): mixed
    {
        return $this->populationService->get($id);
    }

    #[Put]
    #[Incoming(schema: Model\Entity::class)]
    #[Outgoing(code: 200, schema: Model\Message::class)]
    public function doPut(int $id, Model\Entity $payload): array
    {
        $this->populationService->update(
            $id,
            $payload->getPlace(),
            $payload->getRegion(),
            $payload->getPopulation(),
            $payload->getUsers(),
            $payload->getWorldUsers()
        );

        return [
            'success' => true,
            'message' => 'Update successful',
        ];
    }

    #[Delete]
    #[Outgoing(code: 200, schema: Model\Message::class)]
    public function doDelete(int $id): array
    {
        $this->populationService->delete($id);

        return [
            'success' => true,
            'message' => 'Delete successful',
        ];
    }
}
