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

use Doctrine\DBAL;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use PSX\Data\Configuration;
use PSX\Data\Processor;
use PSX\Data\WriterInterface;
use PSX\Dependency\AutowireResolver;
use PSX\Dependency\AutowireResolverInterface;
use PSX\Dependency\Container;
use PSX\Dependency\Inspector\CachedInspector;
use PSX\Dependency\Inspector\ContainerInspector;
use PSX\Dependency\InspectorInterface;
use PSX\Dependency\ObjectBuilder;
use PSX\Dependency\ObjectBuilderInterface;
use PSX\Dependency\TagResolver;
use PSX\Dependency\TagResolverInterface;
use PSX\Dependency\TypeResolver;
use PSX\Dependency\TypeResolverInterface;
use PSX\Framework\Log\ErrorFormatter;
use PSX\Framework\Log\LogListener;
use PSX\Http;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaManagerInterface;
use PSX\Sql\Logger as SqlLogger;
use PSX\Sql\TableManager;
use PSX\Sql\TableManagerInterface;
use PSX\Validate\Validate;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * DefaultContainer
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DefaultContainer extends Container
{
    use Framework;
    use Console;

    public function getCache(): CacheItemPoolInterface
    {
        return $this->newSymfonyCacheImpl('psx');
    }

    public function getConnection(): DBAL\Connection
    {
        $params = $this->get('config')->get('psx_connection');
        $config = new DBAL\Configuration();
        $config->setSQLLogger(new SqlLogger($this->get('logger')));

        return DBAL\DriverManager::getConnection($params, $config);
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        $eventDispatcher = new EventDispatcher();

        $this->appendDefaultListener($eventDispatcher);

        return $eventDispatcher;
    }

    public function getHttpClient(): Http\Client\ClientInterface
    {
        return new Http\Client\Client();
    }

    public function getIo(): Processor
    {
        $config = Configuration::createDefault(
            $this->get('schema_manager')
        );

        return new Processor($config);
    }

    public function getLogger(): LoggerInterface
    {
        $logger = new Logger('psx');
        $logger->pushHandler($this->newLoggerHandlerImpl());

        return $logger;
    }

    public function getSchemaManager(): SchemaManagerInterface
    {
        return new SchemaManager(
            $this->get('cache'),
            $this->get('config')->get('psx_debug')
        );
    }

    public function getTableManager(): TableManagerInterface
    {
        return new TableManager($this->get('connection'));
    }

    public function getValidate(): Validate
    {
        return new Validate();
    }

    public function getContainerAutowireResolver(): AutowireResolverInterface
    {
        return new AutowireResolver(
            $this->get('container_type_resolver')
        );
    }

    public function getContainerInspector(): InspectorInterface
    {
        $inspector = new ContainerInspector(
            $this
        );

        if (!$this->get('config')->get('psx_debug')) {
            $inspector = new CachedInspector($inspector, $this->get('cache'));
        }

        return $inspector;
    }

    public function getObjectBuilder(): ObjectBuilderInterface
    {
        return new ObjectBuilder(
            $this->get('container_type_resolver'),
            $this->get('cache'),
            $this->get('config')->get('psx_debug')
        );
    }

    public function getContainerTagResolver(): TagResolverInterface
    {
        return new TagResolver(
            $this,
            $this->get('container_inspector')
        );
    }

    public function getContainerTypeResolver(): TypeResolverInterface
    {
        return new TypeResolver(
            $this,
            $this->get('container_inspector')
        );
    }

    protected function appendDefaultConfig()
    {
        return [
            'psx_dispatch'            => 'index.php/',
            'psx_timezone'            => 'UTC',
            'psx_error_controller'    => null,
            'psx_error_template'      => null,
            'psx_connection'          => [
                'memory'              => true,
                'driver'              => 'pdo_sqlite',
            ],
            'psx_entity_paths'        => [],
            'psx_soap_namespace'      => 'http://phpsx.org/2014/data',
            'psx_json_namespace'      => 'urn:schema.phpsx.org#',
            'psx_cors_origin'         => '*',
            'psx_cors_headers'        => ['Accept', 'Accept-Language', 'Authorization', 'Content-Language', 'Content-Type'],
            'psx_context_factory'     => null,
            'psx_cache_factory'       => null,
            'psx_logger_factory'      => null,
            'psx_filter_pre'          => [],
            'psx_filter_post'         => [],
            'psx_events'              => [],
            'psx_supported_writer'    => [
                WriterInterface::ATOM,
                WriterInterface::FORM,
                WriterInterface::JSON,
                WriterInterface::JSONP,
                WriterInterface::JSONX,
                WriterInterface::RSS,
                WriterInterface::SOAP,
                WriterInterface::XML,
            ],
        ];
    }

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    protected function appendDefaultListener(EventDispatcherInterface $eventDispatcher)
    {
        $events = $this->get('config')->get('psx_events');
        if (!empty($events)) {
            $builder = $this->get('object_builder');

            foreach ($events as $eventName => $event) {
                if (is_string($event)) {
                    $eventDispatcher->addSubscriber($builder->getObject($event, [], EventSubscriberInterface::class));
                } elseif ($event instanceof EventSubscriberInterface) {
                    $eventDispatcher->addSubscriber($event);
                } elseif ($event instanceof \Closure) {
                    $eventDispatcher->addListener($eventName, $event);
                } else {
                    throw new \RuntimeException('Event must be either a classname, instance of Symfony\Component\EventDispatcher\EventSubscriberInterface or closure');
                }
            }
        }

        $eventDispatcher->addSubscriber(new LogListener($this->get('logger')));
    }

    protected function newLoggerHandlerImpl(): HandlerInterface
    {
        $config  = $this->get('config');
        $factory = $config->get('psx_logger_factory');

        if ($factory instanceof \Closure) {
            return $factory($config);
        } else {
            $level = $config->get('psx_log_level');
            $level = !empty($level) ? $level : Logger::ERROR;

            $handler = new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, $level, true, true);
            $handler->setFormatter(new ErrorFormatter());

            return $handler;
        }
    }

    protected function newSymfonyCacheImpl(string $namespace): AdapterInterface
    {
        $config  = $this->get('config');
        $factory = $config->get('psx_cache_factory');

        if ($factory instanceof \Closure) {
            return $factory($config, $namespace);
        } else {
            return new FilesystemAdapter($namespace, 0, $this->get('config')->get('psx_path_cache') . '/' . $namespace);
        }
    }
}
