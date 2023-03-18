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

namespace PSX\Framework\Loader\RoutingParser;

use PSX\Api\Scanner\FilterInterface;
use PSX\Api\Parser\Attribute\Meta;
use PSX\Framework\Loader\RoutingCollection;
use PSX\Framework\Loader\RoutingParserInterface;

/**
 * Uses the attributes at a controller to define the routing
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AttributeParser implements RoutingParserInterface
{
    private iterable $controllerCollection;

    public function __construct(iterable $controllerCollection)
    {
        $this->controllerCollection = $controllerCollection;
    }

    public function getCollection(?FilterInterface $filter = null): RoutingCollection
    {
        $result = new RoutingCollection();

        foreach ($this->controllerCollection as $controller) {
            $reflection = new \ReflectionClass(get_class($controller));
            $rootMeta = Meta::fromAttributes($reflection->getAttributes());

            foreach ($reflection->getMethods() as $method) {
                $meta = Meta::fromAttributes($method->getAttributes());
                $meta->merge($rootMeta);

                $httpMethod = $meta->getMethod()?->method;
                $httpPath = $meta->getPath()?->path;

                if (!empty($httpMethod) && !empty($httpPath)) {
                    $methods = [$httpMethod, 'OPTIONS'];
                    if ($httpMethod === 'GET') {
                        $methods[] = 'HEAD';
                    }
                    $result->add($methods, $httpPath, [get_class($controller), $method->getName()]);
                }
            }
        }

        return $result;
    }
}
