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

use PSX\Api\Attribute\Description;
use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Post;
use PSX\Api\Attribute\QueryParam;
use PSX\Framework\App\Model\Collection;
use PSX\Framework\App\Model\Entity;
use PSX\Framework\App\Model\Message;
use PSX\Framework\App\Service\Population;
use PSX\Framework\Controller\ControllerAbstract;

#[Description('Collection endpoint')]
#[Path('/population/popo')]
class CollectionPopo extends ControllerAbstract
{
    private Population $populationService;

    public function __construct(Population $populationService)
    {
        $this->populationService = $populationService;
    }

    #[Get]
    #[QueryParam(name: "startIndex", type: "integer")]
    #[QueryParam(name: "count", type: "integer")]
    #[Outgoing(code: 200, schema: Collection::class)]
    protected function doGet(int $startIndex, int $count): mixed
    {
        return $this->populationService->getAll(
            $startIndex,
            $count
        );
    }

    #[Post]
    #[Incoming(schema: Entity::class)]
    #[Outgoing(code: 201, schema: Message::class)]
    protected function doPost(Entity $record): Message
    {
        $this->populationService->create(
            $record->getPlace(),
            $record->getRegion(),
            $record->getPopulation(),
            $record->getUsers(),
            $record->getWorldUsers()
        );

        $message = new Message();
        $message->setSuccess(true);
        $message->setMessage('Create population successful');
        return $message;
    }
}
