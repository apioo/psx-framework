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

namespace PSX\Framework\Tests\Loader\RoutingParser;

use PHPUnit\Framework\TestCase;
use PSX\Framework\Loader\RoutingCollection;

/**
 * RoutingParserTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class RoutingParserTestCase extends TestCase
{
    public function testGetCollection()
    {
        $collection  = $this->getRoutingCollection();

        $this->assertInstanceOf(RoutingCollection::class, $collection);

        $iterator = $collection->getIterator();
        $routing = $iterator->current();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals(['PSX\Framework\Loader\Foo1Controller', 'show'], $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(0, $iterator->key());

        $iterator->next();
        $routing = $iterator->current();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/foo/bar', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals(['PSX\Framework\Loader\Foo2Controller', 'show'], $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(1, $iterator->key());

        $iterator->next();
        $routing = $iterator->current();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/foo/:bar', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals(['PSX\Framework\Loader\Foo3Controller', 'show'], $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(2, $iterator->key());

        $iterator->next();
        $routing = $iterator->current();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/foo/:bar/:foo', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals(['PSX\Framework\Loader\Foo4Controller', 'show'], $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(3, $iterator->key());

        $iterator->next();
        $routing = $iterator->current();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals(['PSX\Framework\Loader\Foo5Controller', 'show'], $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(4, $iterator->key());

        $iterator->next();
        $routing = $iterator->current();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar/foo', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals(['PSX\Framework\Loader\Foo6Controller', 'show'], $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(5, $iterator->key());

        $iterator->next();
        $routing = $iterator->current();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar/$foo<[0-9]+>', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals(['PSX\Framework\Loader\Foo7Controller', 'show'], $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(6, $iterator->key());

        $iterator->next();
        $routing = $iterator->current();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar/$foo<[0-9]+>/$bar<[0-9]+>', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals(['PSX\Framework\Loader\Foo8Controller', 'show'], $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(7, $iterator->key());

        $iterator->next();
        $routing = $iterator->current();

        $this->assertEquals(['POST'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals(['PSX\Framework\Loader\Foo9Controller', 'show'], $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(8, $iterator->key());

        $iterator->next();
        $routing = $iterator->current();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/whitespace', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals(['PSX\Framework\Loader\Foo10Controller', 'show'], $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(9, $iterator->key());

        $iterator->next();
        $routing = $iterator->current();

        $this->assertEquals(['GET', 'POST'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/test', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals(['PSX\Framework\Loader\Foo11Controller', 'show'], $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(10, $iterator->key());

        $iterator->next();
        $routing = $iterator->current();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/alias', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('~/foo/bar', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(11, $iterator->key());

        $iterator->next();
        $routing = $iterator->current();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/files/*path', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals(['PSX\Framework\Loader\Foo12Controller', 'show'], $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(12, $iterator->key());

        $iterator->next();
        $routing = $iterator->current();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('http://cdn.foo.com/serve/*path', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals(['PSX\Framework\Loader\Foo13Controller', 'show'], $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(13, $iterator->key());

        $iterator->next();
        $routing = $iterator->current();

        $this->assertEquals(['ANY'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/baz', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals(['PSX\Framework\Loader\Foo14Controller', 'show'], $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(14, $iterator->key());

        // test traversable
        foreach ($collection as $route) {
        }
    }
    
    abstract public function getRoutingCollection();
}
