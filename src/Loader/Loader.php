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
    /**
     * @var \PSX\Framework\Loader\LocationFinderInterface
     */
    protected $locationFinder;

    /**
     * @var \PSX\Framework\Dispatch\ControllerFactoryInterface
     */
    protected $controllerFactory;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \PSX\Framework\Config\Config
     */
    protected $config;

    /**
     * @param \PSX\Framework\Loader\LocationFinderInterface $locationFinder
     * @param \PSX\Framework\Dispatch\ControllerFactoryInterface $controllerFactory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Psr\Log\LoggerInterface $logger
     * @param \PSX\Framework\Config\Config $config
     */
    public function __construct(LocationFinderInterface $locationFinder, ControllerFactoryInterface $controllerFactory, EventDispatcherInterface $eventDispatcher, LoggerInterface $logger, Config $config)
    {
        $this->locationFinder    = $locationFinder;
        $this->controllerFactory = $controllerFactory;
        $this->eventDispatcher   = $eventDispatcher;
        $this->logger            = $logger;
        $this->config            = $config;
    }

    /**
     * @inheritdoc
     */
    public function load(RequestInterface $request, ResponseInterface $response, Context $context = null)
    {
        $context = $context ?? new Context();
        $result  = $this->locationFinder->resolve($request, $context);

        if ($result instanceof RequestInterface) {
            $this->eventDispatcher->dispatch(Event::ROUTE_MATCHED, new RouteMatchedEvent($result, $context));

            $controller = $this->controllerFactory->getController($context->getSource(), $context);

            $this->execute($controller, $result, $response);
        } else {
            throw new InvalidPathException('Unknown location', $request);
        }
    }

    /**
     * @inheritdoc
     */
    public function execute($controller, RequestInterface $request, ResponseInterface $response)
    {
        $this->eventDispatcher->dispatch(Event::CONTROLLER_EXECUTE, new ControllerExecuteEvent($controller, $request, $response));

        $filters = array_merge(
            $this->controllerFactory->getController($this->config->get('psx_filter_pre')),
            $controller,
            $this->controllerFactory->getController($this->config->get('psx_filter_post'))
        );

        $filterChain = new FilterChain($filters);
        $filterChain->setLogger($this->logger);
        $filterChain->handle($request, $response);

        $this->eventDispatcher->dispatch(Event::CONTROLLER_PROCESSED, new ControllerProcessedEvent($controller, $request, $response));
    }
}
