<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use PSX\Framework\Annotation\Inject;
use ReflectionClass;
use RuntimeException;

/**
 * ObjectBuilder
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ObjectBuilder implements ObjectBuilderInterface
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $reader;

    /**
     * @var \Psr\Cache\CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @var boolean
     */
    protected $debug;

    public function __construct(ContainerInterface $container, Reader $reader, CacheItemPoolInterface $cache, $debug)
    {
        $this->container = $container;
        $this->reader    = $reader;
        $this->cache     = $cache;
        $this->debug     = $debug;
    }

    public function getObject($className, array $constructorArguments = array(), $instanceOf = null)
    {
        $class = new ReflectionClass($className);

        if ($class->getConstructor() === null) {
            $object = $class->newInstanceArgs([]);
        } else {
            $object = $class->newInstanceArgs($constructorArguments);
        }

        if ($instanceOf !== null && !$object instanceof $instanceOf) {
            throw new InvalidArgumentException('Class ' . $className . ' must be an instanceof ' . $instanceOf);
        }

        // if we are not in debug mode we can cache the dependency annotations
        // of each class so we do not need to parse the annotations
        if (!$this->debug) {
            $key  = __CLASS__ . $className;
            $item = $this->cache->getItem($key);

            if ($item->isHit()) {
                $properties = $item->get();
            } else {
                $properties = $this->getProperties($class);

                $item->set($properties);
                $this->cache->save($item);
            }
        } else {
            $properties = $this->getProperties($class);
        }

        foreach ($properties as $propertyName => $service) {
            if ($this->container->has($service)) {
                $property = $class->getProperty($propertyName);
                $property->setAccessible(true);
                $property->setValue($object, $this->container->get($service));
            } else {
                throw new RuntimeException('Trying to inject a not existing service ' . $service);
            }
        }

        return $object;
    }

    private function getProperties(ReflectionClass $class)
    {
        $properties = $class->getProperties();
        $result     = [];

        foreach ($properties as $property) {
            $inject = $this->reader->getPropertyAnnotation($property, '\\PSX\\Framework\\Annotation\\Inject');
            if ($inject instanceof Inject) {
                $service = $inject->getService();
                if (empty($service)) {
                    $service = $property->getName();
                }

                $result[$property->getName()] = $service;
            }
        }

        return $result;
    }
}
