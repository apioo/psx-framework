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

namespace PSX\Framework\Dispatch;

use Psr\Container\ContainerInterface;
use PSX\Api\DocumentedInterface;
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
    /**
     * @var \PSX\Dependency\ObjectBuilderInterface
     */
    protected $objectBuilder;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @param \PSX\Dependency\ObjectBuilderInterface $objectBuilder
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(ObjectBuilderInterface $objectBuilder, ContainerInterface $container)
    {
        $this->objectBuilder = $objectBuilder;
        $this->container     = $container;
    }

    /**
     * @param string $source
     * @param \PSX\Framework\Loader\Context $context
     * @return array
     */
    public function getController($source, Context $context = null)
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
                $controller = array_merge($controller, $this->getController($value, $context));
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

    /**
     * @param string $className
     * @param Context|null $context
     * @param string|null $version
     * @return \PSX\Api\Resource|null
     */
    public function getDocumentation($className, Context $context = null, $version = null)
    {
        try {
            $controller = $this->getController($className, $context);

            if (empty($controller)) {
                return null;
            }

            foreach ($controller as $con) {
                if ($con instanceof DocumentedInterface) {
                    return $con->getDocumentation($version);
                }
            }
        } catch (\Throwable $e) {
        }

        return null;
    }
}
