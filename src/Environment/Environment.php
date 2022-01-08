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

namespace PSX\Framework\Environment;

use Psr\Container\ContainerInterface;
use PSX\Engine\DispatchInterface;
use PSX\Engine\EngineInterface;
use PSX\Engine\WebServer\Engine;
use PSX\Framework\Bootstrap;
use PSX\Framework\Config\Config;

/**
 * Environment
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Environment
{
    private DispatchInterface $dispatch;
    private EngineInterface $engine;
    private Config $config;

    public function __construct(DispatchInterface $dispatch, EngineInterface $engine, Config $config)
    {
        $this->dispatch = $dispatch;
        $this->engine   = $engine;
        $this->config   = $config;
    }

    public function serve(): void
    {
        Bootstrap::setupEnvironment($this->config);

        $this->engine->serve($this->dispatch);
    }

    /**
     * Creates a new PSX environment based on a DI container. You can optional provide
     * a different engine to support different web servers
     * 
     * @link https://github.com/apioo/psx-engine
     * @param ContainerInterface $container
     * @param EngineInterface|null $engine
     * @return Environment
     */
    public static function fromContainer(ContainerInterface $container, ?EngineInterface $engine = null): Environment
    {
        $dispatch = $container->get('dispatch');
        $config   = $container->get('config');

        if ($engine === null) {
            $engine = new Engine($config->get('psx_url'));
        }

        return new self($dispatch, $engine, $config);
    }
}
