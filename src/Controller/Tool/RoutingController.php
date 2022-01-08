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

namespace PSX\Framework\Controller\Tool;

use PSX\Api\Attribute\Outgoing;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Loader\RoutingParserInterface;
use PSX\Framework\Schema;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Record\Record;

/**
 * RoutingController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RoutingController extends ControllerAbstract
{
    #[Inject]
    private RoutingParserInterface $routingParser;

    #[Outgoing(code: 200, schema: Schema\Routing\Collection::class)]
    protected function doGet(HttpContextInterface $context): array
    {
        return [
            'routings' => $this->getRoutings(),
        ];
    }

    private function getRoutings(): array
    {
        $result   = array();
        $routings = $this->routingParser->getCollection()->getAll();

        foreach ($routings as $routing) {
            [$methods, $path, $source] = $routing;

            $result[] = Record::fromArray([
                'methods' => $methods,
                'path'    => $path,
                'source'  => $source,
            ]);
        }

        return $result;
    }
}
