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

namespace PSX\Framework\Tests\Loader\LocationFinder;

use PSX\Framework\Loader\Context;
use PSX\Framework\Loader\LocationFinder\RoutingParser;
use PSX\Framework\Loader\RoutingParser\RoutingFile;
use PSX\Http\Request;
use PSX\Uri\Uri;

/**
 * RoutingParserTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RoutingParserTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalRoute()
    {
        $context = $this->resolve('GET', '');
        $this->assertEquals('PSX\Framework\Loader\Foo1Controller', $context->getSource());
        $this->assertEquals([], $context->getParameters());

        $context = $this->resolve('GET', '/');
        $this->assertEquals('PSX\Framework\Loader\Foo1Controller', $context->getSource());
        $this->assertEquals([], $context->getParameters());

        $context = $this->resolve('GET', '/foo/bar');
        $this->assertEquals('PSX\Framework\Loader\Foo2Controller', $context->getSource());
        $this->assertEquals([], $context->getParameters());

        $context = $this->resolve('GET', '/foo/test');
        $this->assertEquals('PSX\Framework\Loader\Foo3Controller', $context->getSource());
        $this->assertEquals(['bar' => 'test'], $context->getParameters());

        $context = $this->resolve('GET', '/foo/test/bar');
        $this->assertEquals('PSX\Framework\Loader\Foo4Controller', $context->getSource());
        $this->assertEquals(['bar' => 'test', 'foo' => 'bar'], $context->getParameters());

        $context = $this->resolve('GET', '/bar');
        $this->assertEquals('PSX\Framework\Loader\Foo5Controller', $context->getSource());
        $this->assertEquals([], $context->getParameters());

        $context = $this->resolve('GET', '/bar/foo');
        $this->assertEquals('PSX\Framework\Loader\Foo6Controller', $context->getSource());
        $this->assertEquals([], $context->getParameters());

        $context = $this->resolve('GET', '/bar/14');
        $this->assertEquals('PSX\Framework\Loader\Foo7Controller', $context->getSource());
        $this->assertEquals(['foo' => '14'], $context->getParameters());

        $context = $this->resolve('GET', '/bar/14/16');
        $this->assertEquals('PSX\Framework\Loader\Foo8Controller', $context->getSource());
        $this->assertEquals(['foo' => '14', 'bar' => '16'], $context->getParameters());

        $context = $this->resolve('POST', '/bar');
        $this->assertEquals('PSX\Framework\Loader\Foo9Controller', $context->getSource());
        $this->assertEquals([], $context->getParameters());

        $context = $this->resolve('GET', '/whitespace');
        $this->assertEquals('PSX\Framework\Loader\Foo10Controller', $context->getSource());
        $this->assertEquals([], $context->getParameters());

        $context = $this->resolve('GET', '/alias');
        $this->assertEquals('PSX\Framework\Loader\Foo2Controller', $context->getSource());
        $this->assertEquals([], $context->getParameters());

        $context = $this->resolve('GET', '/files/foo/bar/foo.htm');
        $this->assertEquals('PSX\Framework\Loader\Foo12Controller', $context->getSource());
        $this->assertEquals(['path' => 'foo/bar/foo.htm'], $context->getParameters());

        // we can not resolve controller Foo13Controller since it has a static
        // url this is only useful for the reverse router

        $context = $this->resolve('GET', '/baz');
        $this->assertEquals('PSX\Framework\Loader\Foo14Controller', $context->getSource());
        $this->assertEquals([], $context->getParameters());

        $context = $this->resolve('PATCH', '/baz');
        $this->assertEquals('PSX\Framework\Loader\Foo14Controller', $context->getSource());
        $this->assertEquals([], $context->getParameters());
    }

    public function testInvalidRoute()
    {
        $context = $this->resolve('/foo/baz', 'GET');

        $this->assertEquals(null, $context->getSource());
        $this->assertEquals([], $context->getParameters());
    }

    public function testRegexpRoute()
    {
        $context = $this->resolve('GET', '/bar/foo/16');

        $this->assertEquals(null, $context->getSource());
        $this->assertEquals([], $context->getParameters());
    }

    protected function resolve($method, $path)
    {
        $context = new Context();
        $request = new Request(new Uri($path), $method);

        $locationFinder = new RoutingParser(new RoutingFile(__DIR__ . '/../routes'));
        $locationFinder->resolve($request, $context);

        return $context;
    }
}
