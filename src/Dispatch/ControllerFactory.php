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

namespace PSX\Framework\Dispatch;

use Psr\Container\ContainerInterface;
use PSX\Dependency\ObjectBuilderInterface;
use PSX\Framework\Loader\Context;
use PSX\Http\FilterInterface;

/**
 * ControllerFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ControllerFactory implements ControllerFactoryInterface
{
    private ObjectBuilderInterface $objectBuilder;
    private ContainerInterface $container;

    public function __construct(ObjectBuilderInterface $objectBuilder, ContainerInterface $container)
    {
        $this->objectBuilder = $objectBuilder;
        $this->container     = $container;
    }

    public function getController(mixed $source, ?Context $context = null): array
    {
        if (is_string($source)) {
            // through the object builder the class can access all services
            // with the @Inject annotation
            $controller = $this->objectBuilder->getObject($source, [$context]);
        } elseif ($source instanceof \Closure) {
            // bind the container to the closure so that it is possible to
            // access services from the DIC
            $source->bindTo($this->container);

            $controller = $source;
        } elseif (is_array($source)) {
            // we have as source an array of middleware. Its important that we
            // resolve those only in case of an actual array
            $controller = [];
            foreach ($source as $value) {
                $controller[] = $this->getController($value, $context);
            }

            return $controller;
        } else {
            $controller = $source;
        }

        if ($controller instanceof \Traversable) {
            return iterator_to_array($controller);
        } elseif ($controller instanceof FilterInterface) {
            return [$controller];
        } elseif ($controller instanceof \Closure) {
            return [$controller];
        } elseif (is_callable($controller)) {
            return [$controller];
        } else {
            return [];
        }
    }
}
