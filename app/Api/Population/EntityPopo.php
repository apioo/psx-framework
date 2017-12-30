<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

/**
 * @Title("Population")
 * @Description("Entity endpoint")
 * @PathParam(name="id", type="integer", required=true)
 */
class EntityPopo extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Framework\App\Service\Population
     */
    protected $populationService;

    /**
     * @Outgoing(code=200, schema="PSX\Framework\App\Model\Entity")
     */
    protected function doGet()
    {
        return $this->populationService->get(
            $this->pathParameters['id']
        );
    }

    /**
     * @Incoming(schema="PSX\Framework\App\Model\Entity")
     * @Outgoing(code=200, schema="PSX\Framework\App\Model\Message")
     */
    protected function doPut($record)
    {
        $this->populationService->update(
            $this->pathParameters['id'],
            $record->getPlace(),
            $record->getRegion(),
            $record->getPopulation(),
            $record->getUsers(),
            $record->getWorldUsers()
        );

        return [
            'success' => true,
            'message' => 'Update successful',
        ];
    }

    /**
     * @Outgoing(code=200, schema="PSX\Framework\App\Model\Message")
     */
    protected function doDelete($record)
    {
        $this->populationService->delete(
            $this->pathParameters['id']
        );

        return [
            'success' => true,
            'message' => 'Delete successful',
        ];
    }
}
