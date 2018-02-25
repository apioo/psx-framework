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

namespace PSX\Framework\Tests\Dependency;

use Doctrine\Common\Cache\ArrayCache;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use PSX\Framework;
use PSX\Framework\Test\ContainerTestCaseTrait;
use PSX\Framework\Test\Environment;

/**
 * Check whether all default classes are available. We want fix this here 
 * because applications rely on these services
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DefaultContainerTest extends \PHPUnit_Framework_TestCase
{
    use ContainerTestCaseTrait;

    public function testGet()
    {
        $container = Environment::getContainer();

        // console
        $this->assertInstanceOf(\Symfony\Component\Console\Application::class, $container->get('console'));

        // framework
        $this->assertInstanceOf(Framework\Config\Config::class, $container->get('config'));
        $this->assertInstanceOf(Framework\Exception\ConverterInterface::class, $container->get('exception_converter'));
        $this->assertInstanceOf(Framework\Session\Session::class, $container->get('session'));
        $this->assertInstanceOf(Framework\Dispatch\ControllerFactoryInterface::class, $container->get('controller_factory'));
        $this->assertInstanceOf(Framework\Loader\LocationFinderInterface::class, $container->get('loader_location_finder'));
        $this->assertInstanceOf(Framework\Loader\LoaderInterface::class, $container->get('loader'));
        $this->assertInstanceOf(Framework\Dispatch\Dispatch::class, $container->get('dispatch'));
        $this->assertInstanceOf(Framework\Loader\RoutingParserInterface::class, $container->get('routing_parser'));
        $this->assertInstanceOf(Framework\Loader\ReverseRouter::class, $container->get('reverse_router'));
        $this->assertInstanceOf(\PSX\Dependency\ObjectBuilderInterface::class, $container->get('object_builder'));
        $this->assertInstanceOf(\PSX\Api\ListingInterface::class, $container->get('resource_listing'));
        $this->assertInstanceOf(\PSX\Api\ApiManager::class, $container->get('api_manager'));

        // default container
        $this->assertInstanceOf(\Doctrine\Common\Annotations\Reader::class, $container->get('annotation_reader'));
        $this->assertInstanceOf(\Doctrine\Common\Annotations\Reader::class, $container->get('annotation_reader_controller'));
        $this->assertInstanceOf(\Psr\Cache\CacheItemPoolInterface::class, $container->get('cache'));
        $this->assertInstanceOf(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class, $container->get('event_dispatcher'));
        $this->assertInstanceOf(\PSX\Http\Client\ClientInterface::class, $container->get('http_client'));
        $this->assertInstanceOf(\PSX\Data\Processor::class, $container->get('io'));
        $this->assertInstanceOf(\Psr\Log\LoggerInterface::class, $container->get('logger'));
        $this->assertInstanceOf(\PSX\Schema\SchemaManagerInterface::class, $container->get('schema_manager'));
        $this->assertInstanceOf(\PSX\Validate\Validate::class, $container->get('validate'));

        if (Environment::hasConnection()) {
            $this->assertInstanceOf(\Doctrine\DBAL\Connection::class, $container->get('connection'));
            $this->assertInstanceOf(\PSX\Sql\TableManager::class, $container->get('table_manager'));
        }
    }

    public function testCacheFactory()
    {
        $handler = new ArrayCache();
        Environment::getService('config')->set('psx_cache_factory', function() use ($handler){
            return $handler;
        });

        Environment::getContainer()->set('cache', null);

        $item = Environment::getContainer()->get('cache')->getItem('foo');

        $this->assertFalse($item->isHit());

        $item->set('foobar');

        Environment::getContainer()->get('cache')->save($item);

        $this->assertEquals('foobar', $handler->fetch('foo'));
    }

    public function testLogFactory()
    {
        $handler = new StreamHandler('php://memory');
        $handler->setFormatter(new LineFormatter('%channel%.%level_name%: %message% %context% %extra%' . "\n"));
        Environment::getService('config')->set('psx_logger_factory', function() use ($handler){
            return $handler;
        });

        Environment::getContainer()->set('logger', null);
        Environment::getContainer()->get('logger')->info('foo');

        $stream = $handler->getStream();
        rewind($stream);

        $this->assertEquals('psx.INFO: foo [] []', trim(stream_get_contents($stream)));
    }
}
