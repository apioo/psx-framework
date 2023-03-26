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

namespace PSX\Framework\Dependency;

use Psr\Container\ContainerInterface;
use PSX\Framework\Config\ConfigFactory;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * ContainerBuilder
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ContainerBuilder
{
    public static function build(string $appDir, bool $isDebug, ...$containerFiles): ContainerInterface
    {
        $targetFile = $appDir . '/cache/container.php';
        $containerConfigCache = new ConfigCache($targetFile, $isDebug);

        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = self::getContainerBuilder($appDir, $containerFiles);
            $containerBuilder->compile();

            $dumper = new PhpDumper($containerBuilder);

            $containerConfigCache->write($dumper->dump(), $containerBuilder->getResources());
        }

        require_once $targetFile;

        return new \ProjectServiceContainer();
    }

    public static function getContainerBuilder(string $appDir, array $containerFiles): SymfonyContainerBuilder
    {
        $config = ConfigFactory::factory($appDir . '/configuration.php');
        $containerBuilder = new SymfonyContainerBuilder();

        $containerBuilder->setParameter('psx_path_app', $appDir);
        $containerBuilder->setParameter('psx_container_files', $containerFiles);

        foreach ($config as $key => $value) {
            $containerBuilder->setParameter($key, $value);
        }

        $loader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../../resources'));
        foreach ($containerFiles as $containerFile) {
            $loader->load($containerFile);
        }

        return $containerBuilder;
    }
}
