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

use Psr\Container\ContainerInterface;
use PSX\Framework\Controller\FilterAwareInterface;
use PSX\Framework\Filter\ControllerExecutorFactory;
use PSX\Framework\Filter\PostFilterChain;
use PSX\Framework\Filter\PreFilterChain;
use PSX\Http\Filter\FilterChain;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

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
    private ControllerExecutorFactory $controllerExecutorFactory;
    private PreFilterChain $preFilterChain;
    private PostFilterChain $postFilterChain;
    private ContainerInterface $container;

    public function __construct(LocationFinderInterface $locationFinder, ControllerExecutorFactory $controllerExecutorFactory, PreFilterChain $preFilterChain, PostFilterChain $postFilterChain, ContainerInterface $container)
    {
        $this->locationFinder = $locationFinder;
        $this->controllerExecutorFactory = $controllerExecutorFactory;
        $this->preFilterChain = $preFilterChain;
        $this->postFilterChain = $postFilterChain;
        $this->container = $container;
    }

    public function load(RequestInterface $request, ResponseInterface $response, Context $context): void
    {
        $result = $this->locationFinder->resolve($request, $context);

        if ($result instanceof RequestInterface) {
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
            $preFilterChain = new FilterChain($controller->getPreFilter());
            $postFilterChain = new FilterChain($controller->getPostFilter());
        } else {
            $preFilterChain = new FilterChain([]);
            $postFilterChain = new FilterChain([]);
        }

        $filters = [
            $this->preFilterChain,
            $preFilterChain,
            $this->controllerExecutorFactory->factory($controller, $methodName, $context),
            $postFilterChain,
            $this->postFilterChain,
        ];

        $filterChain = new FilterChain($filters);
        $filterChain->handle($request, $response);
    }
}
