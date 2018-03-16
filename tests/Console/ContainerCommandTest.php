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

namespace PSX\Framework\Tests\Console;

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
class ContainerCommandTest extends ControllerTestCase
{
    public function testCommand()
    {
        $command = Environment::getService('console')->find('container');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
        ));

        $actual = $commandTester->getDisplay();
        $expect = <<<TEXT

annotation_reader            \Doctrine\Common\Annotations\Reader
annotation_reader_controller \Doctrine\Common\Annotations\Reader
api_manager                  \PSX\Api\ApiManager
cache                        \Psr\Cache\CacheItemPoolInterface
config                       \PSX\Framework\Config\Config
connection                   \Doctrine\DBAL\Connection
console                      \Symfony\Component\Console\Application
controller_factory           \PSX\Framework\Dispatch\ControllerFactoryInterface
cors_policy                  \PSX\Framework\Http\CorsPolicy
dispatch                     \PSX\Framework\Dispatch\Dispatch
event_dispatcher             \Symfony\Component\EventDispatcher\EventDispatcherInterface 
exception_converter          \PSX\Framework\Exception\ConverterInterface
generator_factory            \PSX\Api\GeneratorFactoryInterface
http_client                  \PSX\Http\Client\ClientInterface
io                           \PSX\Data\Processor
listing_filter_factory       \PSX\Api\Listing\FilterFactoryInterface
loader                       \PSX\Framework\Loader\Loader
loader_location_finder       \PSX\Framework\Loader\LocationFinderInterface
logger                       \Psr\Log\LoggerInterface
object_builder               \PSX\Dependency\ObjectBuilderInterface
population_service           \PSX\Framework\App\Service\Population
request_reader               \PSX\Framework\Http\RequestReader
resource_listing             \PSX\Api\ListingInterface
response_writer              \PSX\Framework\Http\ResponseWriter
reverse_router               \PSX\Framework\Loader\ReverseRouter
routing_parser               \PSX\Framework\Loader\RoutingParserInterface
schema_manager               \PSX\Schema\SchemaManagerInterface
session                      \PSX\Framework\Session\Session
table_manager                \PSX\Sql\TableManagerInterface
validate                     \PSX\Validate\Validate

TEXT;

        Assert::assertStringMatchIgnoreWhitespace($expect, $actual);
    }
}
