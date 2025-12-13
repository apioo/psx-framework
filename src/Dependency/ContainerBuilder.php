<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

use LogicException;
use Psr\Container\ContainerInterface;
use PSX\Framework\Config\ConfigFactory;
use PSX\Framework\Messenger\DefaultBus;
use PSX\Framework\Messenger\DefaultTransport;
use PSX\Framework\Messenger\MessengerPass;
use ReflectionMethod;
use Reflector;
use RuntimeException;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * ContainerBuilder
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ContainerBuilder
{
    public static function build(string $appDir, ?bool $debug, ...$containerFiles): ContainerInterface
    {
        $dotenv = new Dotenv();
        $dotenv->load($appDir . '/.env');

        if ($debug === null) {
            $debug = ($_ENV['APP_DEBUG'] ?? '') === 'true';
        }

        $targetFile = $appDir . '/cache/container.php';
        $containerConfigCache = new ConfigCache($targetFile, $debug);

        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = self::getContainerBuilder($appDir, $containerFiles);
            $containerBuilder->compile();

            $dumper = new PhpDumper($containerBuilder);

            $containerConfigCache->write($dumper->dump(), $containerBuilder->getResources());
        }

        require_once $targetFile;

        if (!class_exists('ProjectServiceContainer')) {
            throw new RuntimeException('Could not find container class');
        }

        $container = new \ProjectServiceContainer();
        if (!$container instanceof ContainerInterface) {
            throw new RuntimeException('Generated container must implement: ' . ContainerInterface::class);
        }

        return $container;
    }

    public static function getContainerBuilder(string $appDir, array $containerFiles): SymfonyContainerBuilder
    {
        $containerBuilder = new SymfonyContainerBuilder();
        $containerBuilder->setParameter('psx_path_app', $appDir);

        $actualFiles = [];
        $loader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../../resources'));
        self::resolve($loader, $containerFiles, $actualFiles);
        self::registerAttributeForAutoconfiguration($containerBuilder);

        $containerBuilder->setParameter('psx_container_files', $actualFiles);

        // load config after the file loader since we have only at the point the env function
        $config = ConfigFactory::factory($appDir);
        foreach ($config as $key => $value) {
            $containerBuilder->setParameter($key, $value);
        }

        // compiler passes
        $containerBuilder->addCompilerPass(new MessengerPass());

        return $containerBuilder;
    }

    private static function resolve(PhpFileLoader $loader, iterable $files, array &$actualFiles): void
    {
        foreach ($files as $file) {
            if (is_string($file)) {
                $actualFiles[] = $file;
                $loader->load($file);
            } elseif (is_callable($file)) {
                $callbackFiles = call_user_func($file);
                if (is_iterable($callbackFiles)) {
                    self::resolve($loader, $callbackFiles, $actualFiles);
                } else {
                    throw new RuntimeException('Provided container file callable must return an iterable');
                }
            } else {
                throw new RuntimeException('Provided an invalid container file, must be either a string or callable');
            }
        }
    }

    private static function registerAttributeForAutoconfiguration(SymfonyContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(AsMessageHandler::class, static function (ChildDefinition $definition, AsMessageHandler $attribute, Reflector $reflector): void {
            $tagAttributes = get_object_vars($attribute);
            $tagAttributes['bus'] = $tagAttributes['bus'] ?? DefaultBus::NAME;
            $tagAttributes['from_transport'] = $tagAttributes['fromTransport'] ?? DefaultTransport::NAME;
            unset($tagAttributes['fromTransport']);
            if ($reflector instanceof ReflectionMethod) {
                if (isset($tagAttributes['method'])) {
                    throw new LogicException(sprintf('AsMessageHandler attribute cannot declare a method on "%s::%s()".', $reflector->class, $reflector->name));
                }
                $tagAttributes['method'] = $reflector->getName();
            }
            $definition->addTag('psx.messenger_handler', $tagAttributes);
        });
    }
}
