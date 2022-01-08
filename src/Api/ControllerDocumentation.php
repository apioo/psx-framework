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

use PSX\Api\Listing\FilterInterface;
use PSX\Api\Listing\Route;
use PSX\Api\ListingInterface;
use PSX\Api\Parser\Attribute;
use PSX\Api\ResourceCollection;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Loader\Context;
use PSX\Framework\Loader\PathMatcher;
use PSX\Framework\Loader\RoutingParserInterface;
use PSX\Schema\Definitions;
use PSX\Schema\SchemaManagerInterface;

/**
 * The documentation how a request and response looks is provided in the
 * controller
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ControllerDocumentation implements ListingInterface
{
    private RoutingParserInterface $routingParser;
    private Attribute $attributeParser;

    public function __construct(RoutingParserInterface $routingParser, SchemaManagerInterface $schemaManager)
    {
        $this->routingParser   = $routingParser;
        $this->attributeParser = new Attribute($schemaManager);
    }

    /**
     * @inheritdoc
     */
    public function getAvailableRoutes(FilterInterface $filter = null): iterable
    {
        $collections = $this->routingParser->getCollection($filter);
        $result      = array();

        foreach ($collections as $collection) {
            [$methods, $path, $source] = $collection;

            if ($filter !== null && !$filter->match($path)) {
                continue;
            }

            $parts = explode('::', $source, 2);
            $className = $parts[0] ?? null;

            if (is_a($className, ControllerAbstract::class, true)) {
                $result[] = new Route($path, $methods, '*');
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function find(string $path, ?string $version = null): ?SpecificationInterface
    {
        $matcher    = new PathMatcher($path);
        $collection = $this->routingParser->getCollection();

        foreach ($collection as $route) {
            [$methods, $path, $source] = $route;

            $parts = explode('::', $source, 2);
            $className = $parts[0] ?? null;

            if (class_exists($className) && $matcher->match($path)) {
                return $this->attributeParser->parse($className, $path);
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function findAll(?string $version = null, FilterInterface $filter = null): SpecificationInterface
    {
        $spec  = new Specification(new ResourceCollection(), new Definitions());
        $index = $this->getAvailableRoutes($filter);

        foreach ($index as $resource) {
            $result = $this->find($resource->getPath(), $version);
            if ($result instanceof SpecificationInterface) {
                $spec->merge($result);
            }
        }

        return $spec;
    }

    /**
     * @param array $route
     * @return \PSX\Framework\Loader\Context
     */
    protected function newContext(array $route)
    {
        $context = new Context();
        $context->setPath($route[1]);

        return $context;
    }
}
