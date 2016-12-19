<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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
use PSX\Framework\Test\ContainerTestCaseTrait;
use PSX\Framework\Test\Environment;

/**
 * Check whether all default classes are available. We want fix this here becase
 * applications rely on these services
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
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
        $this->assertInstanceOf('Symfony\Component\Console\Application', $container->get('console'));
        $this->assertInstanceOf('PSX\Framework\Console\ReaderInterface', $container->get('console_reader'));

        // framework
        $this->assertInstanceOf('PSX\Framework\Config\Config', $container->get('config'));
        $this->assertInstanceOf('PSX\Framework\Template\TemplateInterface', $container->get('template'));
        $this->assertInstanceOf('PSX\Framework\Dependency\ObjectBuilderInterface', $container->get('object_builder'));
        $this->assertInstanceOf('PSX\Framework\Exception\ConverterInterface', $container->get('exception_converter'));
        $this->assertInstanceOf('PSX\Framework\Session\Session', $container->get('session'));
        $this->assertInstanceOf('PSX\Framework\Dispatch\ControllerFactoryInterface', $container->get('application_stack_factory'));
        $this->assertInstanceOf('PSX\Framework\Dispatch\ControllerFactoryInterface', $container->get('controller_factory'));
        $this->assertInstanceOf('PSX\Framework\Dispatch\SenderInterface', $container->get('dispatch_sender'));
        $this->assertInstanceOf('PSX\Framework\Loader\LocationFinderInterface', $container->get('loader_location_finder'));
        $this->assertInstanceOf('PSX\Framework\Loader\CallbackResolverInterface', $container->get('loader_callback_resolver'));
        $this->assertInstanceOf('PSX\Framework\Loader\LoaderInterface', $container->get('loader'));
        $this->assertInstanceOf('PSX\Framework\Dispatch\RequestFactoryInterface', $container->get('request_factory'));
        $this->assertInstanceOf('PSX\Framework\Dispatch\ResponseFactoryInterface', $container->get('response_factory'));
        $this->assertInstanceOf('PSX\Framework\Dispatch\Dispatch', $container->get('dispatch'));
        $this->assertInstanceOf('PSX\Framework\Loader\RoutingParserInterface', $container->get('routing_parser'));
        $this->assertInstanceOf('PSX\Framework\Loader\ReverseRouter', $container->get('reverse_router'));
        $this->assertInstanceOf('PSX\Api\ListingInterface', $container->get('resource_listing'));
        $this->assertInstanceOf('PSX\Api\ApiManager', $container->get('api_manager'));

        // default container
        $this->assertInstanceOf('Doctrine\Common\Annotations\Reader', $container->get('annotation_reader'));
        $this->assertInstanceOf('Doctrine\Common\Annotations\Reader', $container->get('annotation_reader_controller'));
        $this->assertInstanceOf('Psr\Cache\CacheItemPoolInterface', $container->get('cache'));
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventDispatcherInterface', $container->get('event_dispatcher'));
        $this->assertInstanceOf('PSX\Http\Client', $container->get('http_client'));
        $this->assertInstanceOf('PSX\Data\Processor', $container->get('io'));
        $this->assertInstanceOf('Psr\Log\LoggerInterface', $container->get('logger'));
        $this->assertInstanceOf('PSX\Schema\SchemaManagerInterface', $container->get('schema_manager'));
        $this->assertInstanceOf('PSX\Validate\Validate', $container->get('validate'));

        if (Environment::hasConnection()) {
            $this->assertInstanceOf('Doctrine\DBAL\Connection', $container->get('connection'));
            $this->assertInstanceOf('PSX\Sql\TableManager', $container->get('table_manager'));
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
