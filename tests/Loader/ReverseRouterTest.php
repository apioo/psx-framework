<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Tests\Loader;

use PHPUnit\Framework\TestCase;
use PSX\Framework\Loader\ReverseRouter;
use PSX\Framework\Loader\RoutingParser\PhpFile;

/**
 * ReverseRouterTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ReverseRouterTest extends TestCase
{
    public function testGetPathRoutes()
    {
        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('/', $router->getPath(['PSX\Framework\Loader\Foo1Controller', 'show']));
        $this->assertEquals('/foo/bar', $router->getPath(['PSX\Framework\Loader\Foo2Controller', 'show']));
        $this->assertEquals('/foo/test', $router->getPath(['PSX\Framework\Loader\Foo3Controller', 'show'], ['test']));
        $this->assertEquals('/foo/bla/blub', $router->getPath(['PSX\Framework\Loader\Foo4Controller', 'show'], ['bla', 'blub']));
        $this->assertEquals('/bar', $router->getPath(['PSX\Framework\Loader\Foo5Controller', 'show']));
        $this->assertEquals('/bar/foo', $router->getPath(['PSX\Framework\Loader\Foo6Controller', 'show']));
        $this->assertEquals('/bar/12', $router->getPath(['PSX\Framework\Loader\Foo7Controller', 'show'], [12]));
        $this->assertEquals('/bar/37/13', $router->getPath(['PSX\Framework\Loader\Foo8Controller', 'show'], ['bar' => 13, 'foo' => 37]));
        $this->assertEquals('/bar', $router->getPath(['PSX\Framework\Loader\Foo9Controller', 'show']));
        $this->assertEquals('/whitespace', $router->getPath(['PSX\Framework\Loader\Foo10Controller', 'show']));
        $this->assertEquals('/test', $router->getPath(['PSX\Framework\Loader\Foo11Controller', 'show']));
        $this->assertEquals('/files/foo/bar', $router->getPath(['PSX\Framework\Loader\Foo12Controller', 'show'], ['path' => 'foo/bar']));
        $this->assertEquals('http://cdn.foo.com/serve/foo/common.js', $router->getPath(['PSX\Framework\Loader\Foo13Controller', 'show'], ['path' => 'foo/common.js']));
        $this->assertEquals('/baz', $router->getPath(['PSX\Framework\Loader\Foo14Controller', 'show'], []));
    }

    public function testGetPathNamedParameter()
    {
        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('/foo/bla/blub', $router->getPath(['PSX\Framework\Loader\Foo4Controller', 'show'], ['foo' => 'blub', 'bar' => 'bla']));
    }

    public function testGetPathIndexedParameter()
    {
        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('/foo/bla/blub', $router->getPath(['PSX\Framework\Loader\Foo4Controller', 'show'], ['bla', 'blub']));
    }

    public function testGetPathMissingParameter()
    {
        $this->expectException(\InvalidArgumentException::class);

        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $router->getPath(['PSX\Framework\Loader\Foo4Controller', 'show'], ['bla']);
    }

    public function testGetPathRegExpMissingParameter()
    {
        $this->expectException(\InvalidArgumentException::class);

        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $router->getPath(['PSX\Framework\Loader\Foo8Controller', 'show'], ['bla']);
    }

    public function testGetNotExisting()
    {
        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertNull($router->getPath(['Foo\Bar', 'show']));
        $this->assertNull($router->getAbsolutePath(['Foo\Bar', 'show']));
        $this->assertNull($router->getUrl(['Foo\Bar', 'show']));
    }

    public function testGetPath()
    {
        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('/foo/bar', $router->getPath(['PSX\Framework\Loader\Foo2Controller', 'show']));

        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', '');

        $this->assertEquals('/foo/bar', $router->getPath(['PSX\Framework\Loader\Foo2Controller', 'show']));

        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', 'index.php/');

        $this->assertEquals('/foo/bar', $router->getPath(['PSX\Framework\Loader\Foo2Controller', 'show']));

        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('http://cdn.foo.com/serve/foo/common.js', $router->getPath(['PSX\Framework\Loader\Foo13Controller', 'show'], ['path' => 'foo/common.js']));
    }

    public function testGetAbsolutePath()
    {
        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('/foo/bar', $router->getAbsolutePath(['PSX\Framework\Loader\Foo2Controller', 'show']));

        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', '');

        $this->assertEquals('/foo/bar/foo/bar', $router->getAbsolutePath(['PSX\Framework\Loader\Foo2Controller', 'show']));

        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', 'index.php/');

        $this->assertEquals('/foo/bar/index.php/foo/bar', $router->getAbsolutePath(['PSX\Framework\Loader\Foo2Controller', 'show']));

        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('http://cdn.foo.com/serve/foo/common.js', $router->getAbsolutePath(['PSX\Framework\Loader\Foo13Controller', 'show'], ['path' => 'foo/common.js']));
    }

    public function testGetUrl()
    {
        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('http://foo.com/foo/bar', $router->getUrl(['PSX\Framework\Loader\Foo2Controller', 'show']));

        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', '');

        $this->assertEquals('http://foo.com/foo/bar/foo/bar', $router->getUrl(['PSX\Framework\Loader\Foo2Controller', 'show']));

        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', 'index.php/');

        $this->assertEquals('http://foo.com/foo/bar/index.php/foo/bar', $router->getUrl(['PSX\Framework\Loader\Foo2Controller', 'show']));

        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('http://cdn.foo.com/serve/foo/common.js', $router->getUrl(['PSX\Framework\Loader\Foo13Controller', 'show'], ['path' => 'foo/common.js']));
    }

    public function testGetBasePath()
    {
        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('', $router->getBasePath());

        $routingFile = new PhpFile(__DIR__ . '/routes.php');
        $router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', '');

        $this->assertEquals('/foo/bar', $router->getBasePath());
    }
}
