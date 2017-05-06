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

namespace PSX\Framework\Tests\Controller\SchemaApi;

use PSX\Record\Record;
use PSX\Data\Writer;
use PSX\Json\Parser;
use PSX\Framework\Test\ControllerTestCase;

/**
 * NoViewTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class NoViewTest extends ControllerTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('http://127.0.0.1/api', 'GET');
        $body     = Parser::decode((string) $response->getBody(), true);

        $this->assertEquals(405, $response->getStatusCode());

        $this->assertArrayHasKey('success', $body);
        $this->assertArrayHasKey('title', $body);
        $this->assertArrayHasKey('message', $body);
        $this->assertArrayHasKey('trace', $body);
        $this->assertArrayHasKey('context', $body);

        $this->assertEquals(false, $body['success']);
        $this->assertEquals('Method is not allowed', substr($body['message'], 0, 21));
    }

    public function testPost()
    {
        $data     = json_encode(array('userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32+00:00'));
        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Content-Type' => 'application/json'], $data);
        $body     = Parser::decode((string) $response->getBody(), true);

        $this->assertEquals(405, $response->getStatusCode());

        $this->assertArrayHasKey('success', $body);
        $this->assertArrayHasKey('title', $body);
        $this->assertArrayHasKey('message', $body);
        $this->assertArrayHasKey('trace', $body);
        $this->assertArrayHasKey('context', $body);

        $this->assertEquals(false, $body['success']);
        $this->assertEquals('Method is not allowed', substr($body['message'], 0, 21));
    }

    public function testPut()
    {
        $data     = json_encode(array('id' => 1, 'userId' => 3, 'title' => 'foobar'));
        $response = $this->sendRequest('http://127.0.0.1/api', 'PUT', ['Content-Type' => 'application/json'], $data);
        $body     = Parser::decode((string) $response->getBody(), true);

        $this->assertEquals(405, $response->getStatusCode());

        $this->assertArrayHasKey('success', $body);
        $this->assertArrayHasKey('title', $body);
        $this->assertArrayHasKey('message', $body);
        $this->assertArrayHasKey('trace', $body);
        $this->assertArrayHasKey('context', $body);

        $this->assertEquals(false, $body['success']);
        $this->assertEquals('Method is not allowed', substr($body['message'], 0, 21));
    }

    public function testDelete()
    {
        $data     = json_encode(array('id' => 1));
        $response = $this->sendRequest('http://127.0.0.1/api', 'DELETE', ['Content-Type' => 'application/json'], $data);
        $body     = Parser::decode((string) $response->getBody(), true);

        $this->assertEquals(405, $response->getStatusCode());

        $this->assertArrayHasKey('success', $body);
        $this->assertArrayHasKey('title', $body);
        $this->assertArrayHasKey('message', $body);
        $this->assertArrayHasKey('trace', $body);
        $this->assertArrayHasKey('context', $body);

        $this->assertEquals(false, $body['success']);
        $this->assertEquals('Method is not allowed', substr($body['message'], 0, 21));
    }

    protected function getPaths()
    {
        return array(
            [['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Framework\Tests\Controller\Foo\Application\SchemaApi\NoViewController'],
        );
    }
}
