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

namespace PSX\Framework\Tests\Environment\WebServer;

use PSX\Framework\Config\Config;
use PSX\Framework\Environment\WebServer\RequestFactory;

/**
 * The task of the request factory is to recreate the request from the server
 * environment vars. We assume the webserver follows rfc3875
 *
 * @see     http://www.ietf.org/rfc/rfc3875
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createRequestNoPathProvider
     */
    public function testCreateRequestNoPath($uri, $server)
    {
        $factory = new RequestFactory('http://foo.com', $server);
        $request = $factory->createRequest();

        $this->assertEquals($uri, (string) $request->getUri(), var_export($server, true));
    }

    public function createRequestNoPathProvider()
    {
        return [
            ['http://foo.com/', ['REQUEST_URI' => null, 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/', ['REQUEST_URI' => '', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/', ['REQUEST_URI' => '/', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/bar', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/bar#foo', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar/', ['REQUEST_URI' => '/bar/', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/index.php/bar', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar?bar=test', ['REQUEST_URI' => '/bar?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar?bar=test', ['REQUEST_URI' => '/bar?bar=test#foo', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar/?bar=test', ['REQUEST_URI' => '/bar/?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/index.php/?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/index.php?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/backend/token', ['REQUEST_URI' => '/backend/token', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/backend/token', ['REQUEST_URI' => '/index.php/backend/token', 'SERVER_NAME' => 'foo.com']],
        ];
    }

    /**
     * @dataProvider createRequestNoProtocolNoPathProvider
     */
    public function testCreateRequestNoProtocolNoPath($uri, $server)
    {
        $factory = new RequestFactory('//foo.com', $server);
        $request = $factory->createRequest();

        $this->assertEquals($uri, (string) $request->getUri(), var_export($server, true));
    }

    public function createRequestNoProtocolNoPathProvider()
    {
        return [
            ['http://foo.com/', ['REQUEST_URI' => null, 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/', ['REQUEST_URI' => '', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/', ['REQUEST_URI' => '/', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/bar', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/index.php/bar', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar?bar=test', ['REQUEST_URI' => '/bar?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/index.php/?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/index.php?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/backend/token', ['REQUEST_URI' => '/backend/token', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/backend/token', ['REQUEST_URI' => '/index.php/backend/token', 'SERVER_NAME' => 'foo.com']],
        ];
    }

    /**
     * @dataProvider createRequestPathProvider
     */
    public function testCreateRequestPath($uri, $server)
    {
        $factory = new RequestFactory('http://foo.com/sub/folder', $server);
        $request = $factory->createRequest();

        $this->assertEquals($uri, (string) $request->getUri(), var_export($server, true));
    }

    public function createRequestPathProvider()
    {
        return [
            ['http://foo.com/', ['REQUEST_URI' => null, 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/', ['REQUEST_URI' => '/sub', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/', ['REQUEST_URI' => '/sub/', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/', ['REQUEST_URI' => '/sub/folder', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/', ['REQUEST_URI' => '/sub/folder/', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/sub/folder/bar', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar/', ['REQUEST_URI' => '/sub/folder/bar/', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar/?bar=test', ['REQUEST_URI' => '/sub/folder/bar/?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar?bar=test', ['REQUEST_URI' => '/sub/folder/bar?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/sub/folder/?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/sub/folder?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/sub/folder/index.php/?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/sub/folder/index.php?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/sub/folder/index.php/bar', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar/', ['REQUEST_URI' => '/sub/folder/index.php/bar/', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/backend/token', ['REQUEST_URI' => '/backend/token', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/backend/token', ['REQUEST_URI' => '/index.php/backend/token', 'SERVER_NAME' => 'foo.com']],

            // test paths without sub folder
            ['http://foo.com/', ['REQUEST_URI' => null, 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/', ['REQUEST_URI' => '', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/', ['REQUEST_URI' => '/', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/bar', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/index.php/bar', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/bar?bar=test', ['REQUEST_URI' => '/bar?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/index.php/?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/index.php?bar=test', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/backend/token', ['REQUEST_URI' => '/backend/token', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/backend/token', ['REQUEST_URI' => '/index.php/backend/token', 'SERVER_NAME' => 'foo.com']],
        ];
    }

    /**
     * @dataProvider createRequestNoProtocolProvider
     */
    public function testCreateRequestNoProtocol($uri, $server)
    {
        $factory = new RequestFactory('//foo.com', $server);
        $request = $factory->createRequest();

        $this->assertEquals($uri, (string) $request->getUri(), var_export($server, true));
    }

    public function createRequestNoProtocolProvider()
    {
        return [
            ['http://foo.com/', ['SERVER_NAME' => 'foo.com']],
            ['http://foo.com/', ['HTTPS' => '', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/', ['HTTPS' => '0', 'SERVER_NAME' => 'foo.com']],
            ['https://foo.com/', ['HTTPS' => '1', 'SERVER_NAME' => 'foo.com']],
            ['https://foo.com/', ['HTTPS' => 'on', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/', ['HTTPS' => 'off', 'SERVER_NAME' => 'foo.com']],
            ['https://foo.com/', ['HTTPS' => 'ON', 'SERVER_NAME' => 'foo.com']],
            ['http://foo.com/', ['HTTPS' => 'OFF', 'SERVER_NAME' => 'foo.com']],
        ];
    }

    public function testCreateRequestInvalidUrl()
    {
        $factory = new RequestFactory('foobar', ['SERVER_NAME' => 'foo.com']);
        $request = $factory->createRequest();

        $this->assertEquals('http://foo.com/', (string) $request->getUri());
    }

    public function testGetRequestMethod()
    {
        $factory = new RequestFactory('foobar', ['SERVER_NAME' => 'foo.com', 'REQUEST_METHOD' => 'POST']);
        $request = $factory->createRequest();

        $this->assertEquals('POST', $request->getMethod());
    }

    public function testGetRequestMethodOverwrite()
    {
        $factory = new RequestFactory('http://foo.com', ['SERVER_NAME' => 'foo.com', 'REQUEST_METHOD' => 'POST', 'HTTP_X_HTTP_METHOD_OVERRIDE' => 'PUT']);
        $request = $factory->createRequest();

        $this->assertEquals('PUT', $request->getMethod());
    }

    public function testGetRequestMethodOverwriteInvalid()
    {
        $factory = new RequestFactory('http://foo.com', ['SERVER_NAME' => 'foo.com', 'REQUEST_METHOD' => 'POST', 'HTTP_X_HTTP_METHOD_OVERRIDE' => 'FOO']);
        $request = $factory->createRequest();

        $this->assertEquals('POST', $request->getMethod());
    }

    public function testGetRequestHeader()
    {
        $factory = new RequestFactory('http://foo.com', ['SERVER_NAME' => 'foo.com', 'HTTP_FOO_BAR' => 'foobar']);
        $request = $factory->createRequest();

        $this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
    }

    public function testGetRequestHeaderContentHeader()
    {
        $factory = new RequestFactory('http://foo.com', ['SERVER_NAME' => 'foo.com', 'HTTP_FOO_BAR' => 'foobar', 'CONTENT_LENGTH' => 8, 'CONTENT_MD5' => 'foobar', 'CONTENT_TYPE' => 'text/html']);
        $request = $factory->createRequest();

        $this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
        $this->assertEquals(8, $request->getHeader('Content-Length'));
        $this->assertEquals('foobar', $request->getHeader('Content-MD5'));
        $this->assertEquals('text/html', $request->getHeader('Content-Type'));
    }

    public function testGetRequestHeaderRedirectAuthorizationHeader()
    {
        $factory = new RequestFactory('http://foo.com', ['SERVER_NAME' => 'foo.com', 'HTTP_FOO_BAR' => 'foobar', 'REDIRECT_HTTP_AUTHORIZATION' => 'Basic Zm9vOmJhcg==']);
        $request = $factory->createRequest();

        $this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
        $this->assertEquals('Basic Zm9vOmJhcg==', $request->getHeader('Authorization'));
    }

    public function testGetRequestHeaderPhpAuthUser()
    {
        $factory = new RequestFactory('http://foo.com', ['SERVER_NAME' => 'foo.com', 'HTTP_FOO_BAR' => 'foobar', 'PHP_AUTH_USER' => 'foo', 'PHP_AUTH_PW' => 'bar']);
        $request = $factory->createRequest();

        $this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
        $this->assertEquals('Basic Zm9vOmJhcg==', $request->getHeader('Authorization'));
    }

    public function testGetRequestHeaderPhpAuthUserNoPw()
    {
        $factory = new RequestFactory('http://foo.com', ['SERVER_NAME' => 'foo.com', 'HTTP_FOO_BAR' => 'foobar', 'PHP_AUTH_USER' => 'foo', 'PHP_AUTH_PW' => null]);
        $request = $factory->createRequest();

        $this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
        $this->assertEquals('Basic Zm9vOg==', $request->getHeader('Authorization'));
    }

    public function testGetRequestHeaderDigest()
    {
        $factory = new RequestFactory('http://foo.com', ['SERVER_NAME' => 'foo.com', 'HTTP_FOO_BAR' => 'foobar', 'PHP_AUTH_DIGEST' => 'Digest foobar']);
        $request = $factory->createRequest();

        $this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
        $this->assertEquals('Digest foobar', $request->getHeader('Authorization'));
    }
}
