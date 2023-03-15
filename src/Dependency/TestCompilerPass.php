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

namespace PSX\Framework\Dependency;

use Monolog\Logger;
use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use PSX\Api\ScannerInterface;
use PSX\Framework\Loader\LocationFinderInterface;
use PSX\Framework\Loader\RoutingParserInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

/**
 * TestCompilerPass
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestCompilerPass implements CompilerPassInterface
{
    public function process(SymfonyContainerBuilder $container)
    {
        $container->getAlias(LoggerInterface::class)->setPublic(true);
        $container->getAlias(CacheItemPoolInterface::class)->setPublic(true);
        $container->getAlias(EventDispatcherInterface::class)->setPublic(true);
        $container->getAlias(ScannerInterface::class)->setPublic(true);
        $container->getAlias(RoutingParserInterface::class)->setPublic(true);
        $container->getAlias(LocationFinderInterface::class)->setPublic(true);
    }
}
