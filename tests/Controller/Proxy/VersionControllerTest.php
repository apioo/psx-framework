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

namespace PSX\Framework\Tests\Controller\Proxy;

use PSX\Framework\Controller\Proxy\VersionController;
use PSX\Framework\Test\ControllerDbTestCase;
use PSX\Framework\Tests\Controller\Foo\Application\Proxy\TestVersionAcceptController;
use PSX\Framework\Tests\Controller\Foo\Application\Proxy\TestVersionHeaderController;
use PSX\Framework\Tests\Controller\Foo\Application\Proxy\TestVersionUriController;
use PSX\Json\Parser;

/**
 * VersionControllerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class VersionControllerTest extends ControllerDbTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/../../table_fixture.xml');
    }

    public function testAcceptNoVersion()
    {
        $response = $this->sendRequest('/api/accept', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "entry": [
        {
            "id": 4,
            "userId": 3,
            "title": "blub",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 3,
            "userId": 2,
            "title": "test",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 2,
            "userId": 1,
            "title": "bar",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 1,
            "userId": 1,
            "title": "foo",
            "date": "2013-04-29T16:56:32Z"
        }
    ]
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testAcceptExplicitVersion()
    {
        $response = $this->sendRequest('/api/accept', 'GET', ['Accept' => 'application/vnd.psx.v1+json']);
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "entry": [
        {
            "id": 4,
            "userId": 3,
            "title": "blub",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 3,
            "userId": 2,
            "title": "test",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 2,
            "userId": 1,
            "title": "bar",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 1,
            "userId": 1,
            "title": "foo",
            "date": "2013-04-29T16:56:32Z"
        }
    ]
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testAcceptInvalidVersion()
    {
        $response = $this->sendRequest('/api/accept', 'GET', ['Accept' => 'application/vnd.psx.v4+json']);
        $body     = (string) $response->getBody();
        $data     = Parser::decode($body);

        $this->assertEquals(406, $response->getStatusCode(), $body);
        $this->assertEquals(false, $data->success, $body);
        $this->assertEquals('Version is not available', substr($data->message, 0, 24), $body);
    }

    public function testUriNoVersion()
    {
        $response = $this->sendRequest('/api/uri/0', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "entry": [
        {
            "id": 4,
            "userId": 3,
            "title": "blub",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 3,
            "userId": 2,
            "title": "test",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 2,
            "userId": 1,
            "title": "bar",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 1,
            "userId": 1,
            "title": "foo",
            "date": "2013-04-29T16:56:32Z"
        }
    ]
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testUriExplicitVersion()
    {
        $response = $this->sendRequest('/api/uri/1', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "entry": [
        {
            "id": 4,
            "userId": 3,
            "title": "blub",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 3,
            "userId": 2,
            "title": "test",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 2,
            "userId": 1,
            "title": "bar",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 1,
            "userId": 1,
            "title": "foo",
            "date": "2013-04-29T16:56:32Z"
        }
    ]
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testUriInvalidVersion()
    {
        $response = $this->sendRequest('/api/uri/4', 'GET');
        $body     = (string) $response->getBody();
        $data     = Parser::decode($body);

        $this->assertEquals(406, $response->getStatusCode(), $body);
        $this->assertEquals(false, $data->success, $body);
        $this->assertEquals('Version is not available', substr($data->message, 0, 24), $body);
    }

    public function testHeaderNoVersion()
    {
        $response = $this->sendRequest('/api/header', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "entry": [
        {
            "id": 4,
            "userId": 3,
            "title": "blub",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 3,
            "userId": 2,
            "title": "test",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 2,
            "userId": 1,
            "title": "bar",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 1,
            "userId": 1,
            "title": "foo",
            "date": "2013-04-29T16:56:32Z"
        }
    ]
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testHeaderExplicitVersion()
    {
        $response = $this->sendRequest('/api/header', 'GET', ['Api-Version' => 1]);
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "entry": [
        {
            "id": 4,
            "userId": 3,
            "title": "blub",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 3,
            "userId": 2,
            "title": "test",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 2,
            "userId": 1,
            "title": "bar",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 1,
            "userId": 1,
            "title": "foo",
            "date": "2013-04-29T16:56:32Z"
        }
    ]
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testHeaderInvalidVersion()
    {
        $response = $this->sendRequest('/api/header', 'GET', ['Api-Version' => 4]);
        $body     = (string) $response->getBody();
        $data     = Parser::decode($body);

        $this->assertEquals(406, $response->getStatusCode(), $body);
        $this->assertEquals(false, $data->success, $body);
        $this->assertEquals('Version is not available', substr($data->message, 0, 24), $body);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/api/accept', TestVersionAcceptController::class],
            [['GET'], '/api/header', TestVersionHeaderController::class],
            [['GET'], '/api/uri/:version', TestVersionUriController::class],
        );
    }
}
