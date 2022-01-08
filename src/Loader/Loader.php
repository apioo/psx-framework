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

namespace PSX\Framework\Loader;

use Closure;
use Psr\Log\LoggerInterface;
use PSX\Framework\Config\Config;
use PSX\Framework\Dispatch\ControllerFactoryInterface;
use PSX\Framework\Event\ControllerExecuteEvent;
use PSX\Framework\Event\ControllerProcessedEvent;
use PSX\Framework\Event\Event;
use PSX\Framework\Event\RouteMatchedEvent;
use PSX\Http\Filter\FilterChain;
use PSX\Http\FilterInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use UnexpectedValueException;

/**
 * Loader
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Loader implements LoaderInterface
{
    private LocationFinderInterface $locationFinder;
    private ControllerFactoryInterface $controllerFactory;
    private EventDispatcherInterface $eventDispatcher;
    private LoggerInterface $logger;
    private Config $config;

    public function __construct(LocationFinderInterface $locationFinder, ControllerFactoryInterface $controllerFactory, EventDispatcherInterface $eventDispatcher, LoggerInterface $logger, Config $config)
    {
        $this->locationFinder    = $locationFinder;
        $this->controllerFactory = $controllerFactory;
        $this->eventDispatcher   = $eventDispatcher;
        $this->logger            = $logger;
        $this->config            = $config;
    }

    public function load(RequestInterface $request, ResponseInterface $response, ?Context $context = null): void
    {
        $context = $context ?? new Context();
        $result  = $this->locationFinder->resolve($request, $context);

        if ($result instanceof RequestInterface) {
            $this->eventDispatcher->dispatch(new RouteMatchedEvent($result, $context), Event::ROUTE_MATCHED);

            $controller = $this->controllerFactory->getController($context->getSource(), $context);

            $this->execute($controller, $result, $response);
        } else {
            throw new InvalidPathException('Unknown location', $request);
        }
    }

    public function execute(array $controller, RequestInterface $request, ResponseInterface $response): void
    {
        $this->eventDispatcher->dispatch(new ControllerExecuteEvent($controller, $request, $response), Event::CONTROLLER_EXECUTE);

        $filters = array_merge(
            $this->controllerFactory->getController($this->config->get('psx_filter_pre')),
            $controller,
            $this->controllerFactory->getController($this->config->get('psx_filter_post'))
        );

        $filterChain = new FilterChain($filters);
        $filterChain->setLogger($this->logger);
        $filterChain->handle($request, $response);

        $this->eventDispatcher->dispatch(new ControllerProcessedEvent($controller, $request, $response), Event::CONTROLLER_PROCESSED);
    }
}
