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

namespace PSX\Framework\Tests\Controller\Proxy;

use Doctrine\DBAL\Version;
use PSX\Framework\Controller\Proxy\VersionController;
use PSX\Framework\Test\ControllerDbTestCase;
use PSX\Json\Parser;

/**
 * VersionControllerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class VersionControllerTest extends ControllerDbTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/../../table_fixture.xml');
    }

    public function testNoVersion()
    {
        $response = $this->sendRequest('http://127.0.0.1/api', 'GET');
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
        $response = $this->sendRequest('http://127.0.0.1/api', 'GET', ['Accept' => 'application/vnd.psx.v1+json']);
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
        $response = $this->sendRequest('http://127.0.0.1/api', 'GET', ['Accept' => 'application/vnd.psx.v4+json']);
        $body     = (string) $response->getBody();
        $data     = Parser::decode($body);

        $this->assertEquals(406, $response->getStatusCode(), $body);
        $this->assertEquals(false, $data->success, $body);
        $this->assertEquals('Version is not available', substr($data->message, 0, 24), $body);
    }

    public function testUriExplicitVersion()
    {
        $response = $this->sendRequest('http://127.0.0.1/1/api?version_type=' . VersionController::TYPE_URI, 'GET');
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
        $response = $this->sendRequest('http://127.0.0.1/4/api?version_type=' . VersionController::TYPE_URI, 'GET');
        $body     = (string) $response->getBody();
        $data     = Parser::decode($body);

        $this->assertEquals(406, $response->getStatusCode(), $body);
        $this->assertEquals(false, $data->success, $body);
        $this->assertEquals('Version is not available', substr($data->message, 0, 24), $body);
    }

    public function testHeaderExplicitVersion()
    {
        $response = $this->sendRequest('http://127.0.0.1/1/api?version_type=' . VersionController::TYPE_HEADER, 'GET', ['Api-Version' => 1]);
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
        $response = $this->sendRequest('http://127.0.0.1/4/api?version_type=' . VersionController::TYPE_HEADER, 'GET', ['Api-Version' => 4]);
        $body     = (string) $response->getBody();
        $data     = Parser::decode($body);

        $this->assertEquals(406, $response->getStatusCode(), $body);
        $this->assertEquals(false, $data->success, $body);
        $this->assertEquals('Version is not available', substr($data->message, 0, 24), $body);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/api', 'PSX\Framework\Tests\Controller\Foo\Application\Proxy\TestVersionController'],
            [['GET'], '/:version/api', 'PSX\Framework\Tests\Controller\Foo\Application\Proxy\TestVersionController'],
        );
    }
}
