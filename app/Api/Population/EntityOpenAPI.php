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

use PSX\Api\Parser\OpenAPI;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Http\Environment\HttpContextInterface;

/**
 * EntityOpenAPI
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class EntityOpenAPI extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Framework\App\Service\Population
     */
    protected $populationService;

    public function getDocumentation($version = null)
    {
        return OpenAPI::fromFile(__DIR__ . '/../../Resource/population.json', $this->context->getPath());
    }

    protected function doGet(HttpContextInterface $context)
    {
        return $this->populationService->get(
            $context->getUriFragment('id')
        );
    }

    protected function doPut($record, HttpContextInterface $context)
    {
        $this->populationService->update(
            $context->getUriFragment('id'),
            $record['place'],
            $record['region'],
            $record['population'],
            $record['users'],
            $record['worldUsers']
        );

        return [
            'success' => true,
            'message' => 'Update successful',
        ];
    }

    protected function doDelete($record, HttpContextInterface $context)
    {
        $this->populationService->delete(
            $context->getUriFragment('id')
        );

        return [
            'success' => true,
            'message' => 'Delete successful',
        ];
    }
}
