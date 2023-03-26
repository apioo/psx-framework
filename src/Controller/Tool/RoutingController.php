<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Path;
use PSX\Api\Parser\Attribute;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Loader\Context;
use PSX\Framework\Loader\RoutingParserInterface;
use PSX\Framework\Model\RoutingCollection;
use PSX\Framework\Model\RoutingRoute;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Record\Record;

/**
 * RoutingController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class RoutingController extends ControllerAbstract
{
    private RoutingParserInterface $routingParser;

    public function __construct(RoutingParserInterface $routingParser)
    {
        $this->routingParser = $routingParser;
    }

    #[Get]
    #[Path('/system/routing')]
    public function show(): RoutingCollection
    {
        $collection = new RoutingCollection();
        $collection->setRoutings($this->getRoutings());
        return $collection;
    }

    private function getRoutings(): array
    {
        $result   = array();
        $routings = $this->routingParser->getCollection()->getAll();

        foreach ($routings as $routing) {
            [$methods, $path, $source] = $routing;

            if (!isset($methods[0])) {
                continue;
            }

            if (!is_array($source) || count($source) !== 2) {
                continue;
            }

            $router = new RoutingRoute();
            $router->setMethod($methods[0]);
            $router->setPath($path);
            $router->setOperationId(Attribute::buildOperationId($source[0], $source[1]));

            $result[] = $router;
        }

        return $result;
    }
}
