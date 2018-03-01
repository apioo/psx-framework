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

namespace PSX\Framework\Api;

use PSX\Api\DocumentedInterface;
use PSX\Api\Listing\FilterInterface;
use PSX\Api\ListingInterface;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Framework\Dispatch\ControllerFactoryInterface;
use PSX\Framework\Loader\Context;
use PSX\Framework\Loader\PathMatcher;
use PSX\Framework\Loader\RoutingParserInterface;

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
    /**
     * @var \PSX\Framework\Loader\RoutingParserInterface
     */
    protected $routingParser;

    /**
     * @var \PSX\Framework\Dispatch\ControllerFactoryInterface
     */
    protected $controllerFactory;

    /**
     * @param \PSX\Framework\Loader\RoutingParserInterface $routingParser
     * @param \PSX\Framework\Dispatch\ControllerFactoryInterface $controllerFactory
     */
    public function __construct(RoutingParserInterface $routingParser, ControllerFactoryInterface $controllerFactory)
    {
        $this->routingParser     = $routingParser;
        $this->controllerFactory = $controllerFactory;
    }

    /**
     * @inheritdoc
     */
    public function getResourceIndex(FilterInterface $filter = null)
    {
        $collections = $this->routingParser->getCollection();
        $result      = array();

        foreach ($collections as $collection) {
            list($methods, $path, $source) = $collection;

            if ($filter !== null && !$filter->match($path)) {
                continue;
            }

            $parts     = explode('::', $source, 2);
            $className = isset($parts[0]) ? $parts[0] : null;
            $resource  = new Resource(Resource::STATUS_ACTIVE, $path);

            if ($methods === ['ANY']) {
                $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
            }

            foreach ($methods as $methodName) {
                $method = Resource\Factory::getMethod($methodName);

                if ($method instanceof Resource\MethodAbstract) {
                    $resource->addMethod($method);
                }
            }

            // because creating a new instance of a controller is expensive
            // since we resolve all dependencies we use class_implements to
            // check whether this is a documented API endpoint
            if (class_exists($className) && in_array(DocumentedInterface::class, class_implements($className))) {
                $result[] = $resource;
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getResource($sourcePath, $version = null)
    {
        $matcher    = new PathMatcher($sourcePath);
        $collection = $this->routingParser->getCollection();

        foreach ($collection as $route) {
            list($methods, $path, $source) = $route;

            $parts     = explode('::', $source, 2);
            $className = isset($parts[0]) ? $parts[0] : null;

            if (class_exists($className) && $matcher->match($path)) {
                $context    = $this->newContext($route);
                $controller = $this->controllerFactory->getDocumentation($className, $context, $version);

                return $controller;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getResourceCollection($version = null, FilterInterface $filter = null)
    {
        $collection = new ResourceCollection();
        $index      = $this->getResourceIndex($filter);

        foreach ($index as $resource) {
            $collection->set($this->getResource($resource->getPath(), $version));
        }

        return $collection;
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
