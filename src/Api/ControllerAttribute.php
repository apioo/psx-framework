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

namespace PSX\Framework\Api;

use PSX\Api\Operations;
use PSX\Api\Parser\Attribute;
use PSX\Api\Scanner\FilterInterface;
use PSX\Api\ScannerInterface;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Framework\Loader\RoutingParserInterface;
use PSX\Schema\Definitions;
use PSX\Schema\SchemaManagerInterface;

/**
 * The documentation how a request and response looks is provided in the controller
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ControllerAttribute implements ScannerInterface
{
    private RoutingParserInterface $routingParser;
    private Attribute $attributeParser;

    public function __construct(RoutingParserInterface $routingParser, SchemaManagerInterface $schemaManager)
    {
        $this->routingParser   = $routingParser;
        $this->attributeParser = new Attribute($schemaManager);
    }

    public function generate(?FilterInterface $filter = null): SpecificationInterface
    {
        $result = new Specification(new Operations(), new Definitions());

        $collections = $this->routingParser->getCollection($filter);
        foreach ($collections as $collection) {
            [$methods, $path, $source] = $collection;

            if (is_array($source) && count($source) === 2) {
                $controller = $source[0];
                $methodName = $source[1];
            } else {
                continue;
            }

            $spec = $this->attributeParser->parse($controller);

            if ($filter !== null) {
                $spec->getOperations()->filter($filter);
            }

            $result->merge($spec);
        }

        return $result;
    }
}
