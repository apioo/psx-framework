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

namespace PSX\Framework\Tests\Dependency;

use Doctrine\Common\Annotations;
use Doctrine\Common\Cache\ArrayCache;
use PSX\Cache\Pool;
use PSX\Framework\Dependency\Container;
use PSX\Framework\Dependency\ObjectBuilder;
use PSX\Framework\Test\Environment;

/**
 * ObjectBuilderTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ObjectBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetObject()
    {
        $container = new Container();
        $container->set('foo', new \stdClass());
        $container->set('foo_bar', new \DateTime());

        $builder = new ObjectBuilder($container, Environment::getService('annotation_reader_controller'), Environment::getService('cache'), true);
        $object  = $builder->getObject(FooService::class);

        $this->assertInstanceof(FooService::class, $object);
        $this->assertInstanceof(\stdClass::class, $object->getFoo());
        $this->assertInstanceof(\DateTime::class, $object->getBar());
        $this->assertNull($object->getProperty());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetObjectInjectUnknownService()
    {
        $builder = new ObjectBuilder(new Container(), Environment::getService('annotation_reader_controller'), Environment::getService('cache'), true);
        $builder->getObject(FooService::class);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testGetObjectUnknownClass()
    {
        $builder = new ObjectBuilder(new Container(), Environment::getService('annotation_reader_controller'), Environment::getService('cache'), true);
        $builder->getObject('PSX\Framework\Tests\Dependency\BarService');
    }

    public function testGetObjectInstanceOf()
    {
        $container = new Container();
        $container->set('foo', new \stdClass());
        $container->set('foo_bar', new \stdClass());

        $builder = new ObjectBuilder($container, Environment::getService('annotation_reader_controller'), Environment::getService('cache'), true);
        $object  = $builder->getObject(FooService::class, array(), FooService::class);

        $this->assertInstanceof(FooService::class, $object);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetObjectInstanceOfInvalid()
    {
        $container = new Container();
        $container->set('foo', new \stdClass());
        $container->set('foo_bar', new \stdClass());

        $builder = new ObjectBuilder($container, Environment::getService('annotation_reader_controller'), Environment::getService('cache'), true);
        $builder->getObject(FooService::class, array(), 'PSX\Framework\Tests\Dependency\BarService');
    }

    public function testGetObjectConstructorArguments()
    {
        $container = new Container();
        $container->set('foo', new \stdClass());
        $container->set('foo_bar', new \stdClass());

        $builder = new ObjectBuilder($container, Environment::getService('annotation_reader_controller'), Environment::getService('cache'), true);
        $object  = $builder->getObject(FooService::class, array('foo'), FooService::class);

        $this->assertInstanceof(FooService::class, $object);
        $this->assertEquals('foo', $object->getProperty());
    }

    public function testGetObjectWithoutConstructor()
    {
        $builder  = new ObjectBuilder(new Container(), Environment::getService('annotation_reader_controller'), Environment::getService('cache'), true);
        $stdClass = $builder->getObject(\stdClass::class);

        $this->assertInstanceof(\stdClass::class, $stdClass);
    }

    public function testGetObjectCache()
    {
        $container = new Container();
        $container->set('foo', new \stdClass());
        $container->set('foo_bar', new \stdClass());

        $cache   = new Pool(new ArrayCache());
        $builder = new ObjectBuilder($container, Environment::getService('annotation_reader_controller'), $cache, false);
        $object  = $builder->getObject(FooService::class);

        $item = $cache->getItem(ObjectBuilder::class . FooService::class);

        $this->assertInstanceof(FooService::class, $object);
        $this->assertTrue($item->isHit());
        $this->assertEquals(['foo' => 'foo', 'bar' => 'foo_bar'], $item->get());

        $item = $cache->getItem(ObjectBuilder::class . FooService::class);

        $object = $builder->getObject(FooService::class);

        $this->assertInstanceof(FooService::class, $object);
        $this->assertTrue($item->isHit());
        $this->assertEquals(['foo' => 'foo', 'bar' => 'foo_bar'], $item->get());
    }
}
