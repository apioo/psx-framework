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
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Post;
use PSX\Framework\App\Model;
use PSX\Framework\App\Model\Message;
use PSX\Framework\App\Model\PopulationCollection;
use PSX\Framework\App\Service\Population;
use PSX\Framework\App\Table;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Sql\TableManagerInterface;

#[Description('Collection endpoint')]
#[Path('/population/popo')]
class CollectionPopo extends ControllerAbstract
{
    private Population $populationService;
    private Table\Population $populationTable;

    public function __construct(Population $populationService, TableManagerInterface $tableManager)
    {
        $this->populationService = $populationService;
        $this->populationTable = $tableManager->getTable(Table\Population::class);
    }

    #[Get]
    public function doGet(?int $startIndex, ?int $count): PopulationCollection
    {
        return $this->populationTable->getCollection(
            $startIndex,
            $count
        );
    }

    #[Post]
    #[Outgoing(code: 201, schema: Model\Message::class)]
    public function doPost(Model\Population $payload): Model\Message
    {
        $this->populationService->create($payload);

        $message = new Message();
        $message->setSuccess(true);
        $message->setMessage('Create population successful');
        return $message;
    }
}
