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

namespace PSX\Framework\Loader;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use PSX\Framework\Controller\FilterAwareInterface;
use PSX\Framework\Event\Event;
use PSX\Framework\Event\RouteMatchedEvent;
use PSX\Framework\Filter\ControllerExecutorFactory;
use PSX\Framework\Filter\PostFilterCollection;
use PSX\Framework\Filter\PreFilterCollection;
use PSX\Http\Filter\FilterChain;
use PSX\Http\Filter\FilterCollection;
use PSX\Http\FilterCollectionInterface;
use PSX\Http\FilterInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * Loader
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Loader implements LoaderInterface
{
    private LocationFinderInterface $locationFinder;
    private ControllerExecutorFactory $controllerExecutorFactory;
    private FilterCollectionInterface $preFilterCollection;
    private FilterCollectionInterface $postFilterCollection;
    private ContainerInterface $container;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(LocationFinderInterface $locationFinder, ControllerExecutorFactory $controllerExecutorFactory, PreFilterCollection $preFilterCollection, PostFilterCollection $postFilterCollection, ContainerInterface $container, EventDispatcherInterface $eventDispatcher)
    {
        $this->locationFinder = $locationFinder;
        $this->controllerExecutorFactory = $controllerExecutorFactory;
        $this->preFilterCollection = $preFilterCollection;
        $this->postFilterCollection = $postFilterCollection;
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function load(RequestInterface $request, ResponseInterface $response, Context $context): void
    {
        $result = $this->locationFinder->resolve($request, $context);

        if ($result instanceof RequestInterface) {
            $this->eventDispatcher->dispatch(new RouteMatchedEvent($result, $context));

            $this->execute($context->getSource(), $result, $response, $context);
        } else {
            throw new InvalidPathException('Unknown location', $request);
        }
    }

    public function execute(mixed $source, RequestInterface $request, ResponseInterface $response, Context $context): void
    {
        if (is_array($source) && count($source) === 2) {
            $controller = $this->container->get($source[0]);
            $methodName = $source[1];
        } else {
            throw new \RuntimeException('Provided an invalid source');
        }

        if ($controller instanceof FilterAwareInterface) {
            $preFilterCollection = $this->resolveFilter($controller->getPreFilter());
            $postFilterCollection = $this->resolveFilter($controller->getPostFilter());
        } else {
            $preFilterCollection = [];
            $postFilterCollection = [];
        }

        $filterChain = new FilterChain();
        $filterChain->addAll($this->preFilterCollection);
        $filterChain->addAll($preFilterCollection);
        $filterChain->on($this->controllerExecutorFactory->factory($controller, $methodName, $context));
        $filterChain->addAll($postFilterCollection);
        $filterChain->addAll($this->postFilterCollection);

        $filterChain->handle($request, $response);
    }

    private function resolveFilter(array $filter): FilterCollection
    {
        $result = [];
        foreach ($filter as $value) {
            if (is_string($value)) {
                $service = $this->container->get($value);
                if ($service instanceof FilterInterface) {
                    $result[] = $service;
                } else {
                    throw new \RuntimeException('Provided filter service "' . $value . '" must implement: ' . FilterInterface::class);
                }
            } elseif ($value instanceof FilterInterface || $value instanceof \Closure) {
                $result[] = $value;
            } else {
                throw new \RuntimeException('Provided an invalid filter');
            }
        }

        return new FilterCollection($result);
    }
}
