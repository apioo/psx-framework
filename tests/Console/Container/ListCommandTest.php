<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Tests\Console\Container;

use PSX\Framework\Test\Assert;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ContainerCommandTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ListCommandTest extends ControllerTestCase
{
    public function testCommand()
    {
        $command = Environment::getService('console')->find('container:list');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $lines = explode("\n", $commandTester->getDisplay());
        $actual = [];
        foreach ($lines as $line) {
            $parts = array_values(array_filter(explode(' ', trim($line))));
            if (count($parts) === 2) {
                $actual[$parts[1]] = $parts[0];
            }
        }

        $expect = [
            'PSX\\Framework\\Annotation\\ReaderFactory' => 'annotation_reader_factory',
            'PSX\\Api\\ApiManager' => 'api_manager',
            'PSX\\Api\\ApiManagerInterface' => 'api_manager',
            'PSX\\Cache\\Pool' => 'cache',
            'Psr\\Cache\\CacheItemPoolInterface' => 'cache',
            'PSX\\Framework\\Config\\Config' => 'config',
            'Doctrine\\DBAL\\Connection' => 'connection',
            'Symfony\\Component\\Console\\Application' => 'console',
            'PSX\\Dependency\\AutowireResolver' => 'container_autowire_resolver',
            'PSX\\Dependency\\AutowireResolverInterface' => 'container_autowire_resolver',
            'PSX\\Dependency\\InspectorInterface' => 'container_inspector',
            'PSX\\Dependency\\Inspector\\ContainerInspector' => 'container_inspector',
            'PSX\\Dependency\\TagResolverInterface' => 'container_tag_resolver',
            'PSX\\Dependency\\TagResolver' => 'container_tag_resolver',
            'PSX\\Dependency\\TypeResolver' => 'container_type_resolver',
            'PSX\\Dependency\\TypeResolverInterface' => 'container_type_resolver',
            'PSX\\Framework\\Dispatch\\ControllerFactory' => 'controller_factory',
            'PSX\\Framework\\Dispatch\\ControllerFactoryInterface' => 'controller_factory',
            'PSX\\Framework\\Dispatch\\Dispatch' => 'dispatch',
            'Symfony\\Component\\EventDispatcher\\EventDispatcher' => 'event_dispatcher',
            'Symfony\\Component\\EventDispatcher\\EventDispatcherInterface' => 'event_dispatcher',
            'PSX\\Framework\\Exception\\ConverterInterface' => 'exception_converter',
            'PSX\\Framework\\Exception\\Converter' => 'exception_converter',
            'PSX\\Api\\GeneratorFactory' => 'generator_factory',
            'PSX\\Api\\GeneratorFactoryInterface' => 'generator_factory',
            'PSX\\Http\\Client\\Client' => 'http_client',
            'PSX\\Http\\Client\\ClientInterface' => 'http_client',
            'PSX\\Data\\Processor' => 'io',
            'PSX\\Api\\Listing\\FilterFactoryInterface' => 'listing_filter_factory',
            'PSX\\Api\\Listing\\FilterFactory' => 'listing_filter_factory',
            'PSX\\Framework\\Loader\\Loader' => 'loader',
            'PSX\\Framework\\Loader\\LoaderInterface' => 'loader',
            'PSX\\Framework\\Loader\\LocationFinder\\RoutingParser' => 'loader_location_finder',
            'PSX\\Framework\\Loader\\LocationFinderInterface' => 'loader_location_finder',
            'Monolog\\Logger' => 'logger',
            'Psr\\Log\\LoggerInterface' => 'logger',
            'PSX\\Dependency\\ObjectBuilderInterface' => 'object_builder',
            'PSX\\Dependency\\ObjectBuilder' => 'object_builder',
            'PSX\\Framework\\App\\Service\\Population' => 'population_service',
            'PSX\\Framework\\Http\\RequestReader' => 'request_reader',
            'PSX\\Framework\\Api\\ControllerDocumentation' => 'resource_listing',
            'PSX\\Api\\ListingInterface' => 'resource_listing',
            'PSX\\Framework\\Http\\ResponseWriter' => 'response_writer',
            'PSX\\Framework\\Loader\\ReverseRouter' => 'reverse_router',
            'PSX\\Framework\\Loader\\RoutingParserInterface' => 'routing_parser',
            'PSX\\Framework\\Loader\\RoutingParser\\RoutingFile' => 'routing_parser',
            'PSX\\Schema\\SchemaManager' => 'schema_manager',
            'PSX\\Schema\\SchemaManagerInterface' => 'schema_manager',
            'PSX\\Framework\\Session\\Session' => 'session',
            'PSX\\Sql\\TableManagerInterface' => 'table_manager',
            'PSX\\Sql\\TableManager' => 'table_manager',
            'PSX\\Validate\\Validate' => 'validate',
        ];

        $this->assertEquals($expect, $actual);
    }
}
