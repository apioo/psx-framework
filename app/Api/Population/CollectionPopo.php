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

namespace PSX\Framework\App\Api\Population;

use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Http\Environment\HttpContextInterface;

/**
 * @Title("Population")
 * @Description("Collection endpoint")
 */
class CollectionPopo extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Framework\App\Service\Population
     */
    protected $populationService;

    /**
     * @QueryParam(name="startIndex", type="integer")
     * @QueryParam(name="count", type="integer")
     * @Outgoing(code=200, schema="PSX\Framework\App\Model\Collection")
     */
    protected function doGet(HttpContextInterface $context)
    {
        return $this->populationService->getAll(
            $context->getParameter('startIndex'),
            $context->getParameter('count')
        );
    }

    /**
     * @Incoming(schema="PSX\Framework\App\Model\Entity")
     * @Outgoing(code=201, schema="PSX\Framework\App\Model\Message")
     */
    protected function doPost($record, HttpContextInterface $context)
    {
        $this->populationService->create(
            $record->getPlace(),
            $record->getRegion(),
            $record->getPopulation(),
            $record->getUsers(),
            $record->getWorldUsers()
        );

        return [
            'success' => true,
            'message' => 'Create population successful',
        ];
    }
}
