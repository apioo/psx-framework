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

namespace PSX\Framework\Dependency;

use Doctrine\Common\Annotations;
use Doctrine\Common\Cache as DoctrineCache;
use Doctrine\DBAL;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use PSX\Cache;
use PSX\Data\Configuration;
use PSX\Data\Processor;
use PSX\Data\WriterInterface;
use PSX\Dependency\Container;
use PSX\Dependency\ObjectBuilder;
use PSX\Framework\Log\ErrorFormatter;
use PSX\Framework\Log\LogListener;
use PSX\Http;
use PSX\Schema\SchemaManager;
use PSX\Sql\Logger as SqlLogger;
use PSX\Sql\TableManager;
use PSX\Validate\Validate;
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

    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    public function getAnnotationReader()
    {
        return $this->newDoctrineAnnotationImpl([
            'PSX\Schema\Parser\Popo\Annotation',
        ]);
    }

    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    public function getAnnotationReaderController()
    {
        return $this->newDoctrineAnnotationImpl([
            'PSX\Api\Annotation',
            'PSX\Dependency\Annotation',
        ]);
    }

    /**
     * @return \Psr\Cache\CacheItemPoolInterface
     */
    public function getCache()
    {
        return new Cache\Pool($this->newDoctrineCacheImpl('psx'));
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        $params = $this->get('config')->get('psx_connection');
        $config = new DBAL\Configuration();
        $config->setSQLLogger(new SqlLogger($this->get('logger')));

        return DBAL\DriverManager::getConnection($params, $config);
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        $eventDispatcher = new EventDispatcher();

        $this->appendDefaultListener($eventDispatcher);

        return $eventDispatcher;
    }

    /**
     * @return \PSX\Http\Client\ClientInterface
     */
    public function getHttpClient()
    {
        return new Http\Client\Client();
    }

    /**
     * @return \PSX\Data\Processor
     */
    public function getIo()
    {
        $config = Configuration::createDefault(
            $this->get('annotation_reader'),
            $this->get('schema_manager'),
            $this->get('config')->get('psx_soap_namespace')
        );

        return new Processor($config);
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        $logger = new Logger('psx');
        $logger->pushHandler($this->newLoggerHandlerImpl());

        return $logger;
    }

    /**
     * @return \PSX\Schema\SchemaManagerInterface
     */
    public function getSchemaManager()
    {
        return new SchemaManager(
            $this->get('annotation_reader'),
            $this->get('cache'),
            $this->get('config')->get('psx_debug')
        );
    }

    /**
     * @return \PSX\Sql\TableManagerInterface
     */
    public function getTableManager()
    {
        return new TableManager($this->get('connection'));
    }

    /**
     * @return \PSX\Validate\Validate
     */
    public function getValidate()
    {
        return new Validate();
    }

    /**
     * @return \PSX\Dependency\ObjectBuilderInterface
     */
    public function getObjectBuilder()
    {
        return new ObjectBuilder(
            $this,
            $this->get('annotation_reader_controller'),
            $this->get('cache'),
            $this->get('config')->get('psx_debug')
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
            'psx_annotation_autoload' => [
                'PSX\Api\Annotation',
                'PSX\Schema\Parser\Popo\Annotation',
                'PSX\Dependency\Annotation',
                'JMS\Serializer\Annotation',
                'Doctrine\ORM\Mapping'
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

    /**
     * Returns the default log handler
     * 
     * @return \Monolog\Handler\HandlerInterface
     */
    protected function newLoggerHandlerImpl()
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

    /**
     * Returns the default doctrine cache
     * 
     * @param string $namespace
     * @return \Doctrine\Common\Cache\Cache
     */
    protected function newDoctrineCacheImpl($namespace)
    {
        $config  = $this->get('config');
        $factory = $config->get('psx_cache_factory');

        if ($factory instanceof \Closure) {
            return $factory($config, $namespace);
        } else {
            return new DoctrineCache\FilesystemCache($this->get('config')->get('psx_path_cache') . '/' . $namespace);
        }
    }

    /**
     * @param array $namespaces
     * @return \Doctrine\Common\Annotations\Reader
     */
    protected function newDoctrineAnnotationImpl(array $namespaces)
    {
        $reader = new Annotations\SimpleAnnotationReader();

        foreach ($namespaces as $namespace) {
            $reader->addNamespace($namespace);
        }

        if (!$this->get('config')->get('psx_debug')) {
            $reader = new Annotations\CachedReader(
                $reader,
                $this->newDoctrineCacheImpl('annotations/psx'),
                $this->get('config')->get('psx_debug')
            );
        }

        return $reader;
    }
}
