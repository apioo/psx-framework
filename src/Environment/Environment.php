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

namespace PSX\Framework\Environment;

use Psr\Container\ContainerInterface;
use PSX\Framework\Bootstrap;

/**
 * Environment
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Environment
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var \PSX\Framework\Environment\EngineInterface
     */
    protected $engine;

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @param \PSX\Framework\Environment\EngineInterface $engine
     */
    public function __construct(ContainerInterface $container, EngineInterface $engine)
    {
        $this->container = $container;
        $this->engine    = $engine;
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @return mixed
     */
    public function serve()
    {
        $dispatch = $this->container->get('dispatch');
        $config   = $this->container->get('config');

        Bootstrap::setupEnvironment($config);

        return $this->engine->serve($dispatch, $config);
    }
}
