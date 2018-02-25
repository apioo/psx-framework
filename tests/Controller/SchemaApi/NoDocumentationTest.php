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

namespace PSX\Framework\Tests\Controller\SchemaApi;

use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Tests\Controller\Foo\Application\SchemaApi\NoDocumentationController;
use PSX\Http\ResponseInterface;
use PSX\Json\Parser;

/**
 * NoDocumentationTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class NoDocumentationTest extends ControllerTestCase
{
    public function testHead()
    {
        $response = $this->sendRequest('/api', 'HEAD');
        $body     = (string) $response->getBody();

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEmpty($body);
    }

    public function testGet()
    {
        $response = $this->sendRequest('/api', 'GET');

        $this->assertErrorResponse($response);
    }

    public function testPost()
    {
        $data     = json_encode(array('userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32+00:00'));
        $response = $this->sendRequest('/api', 'POST', ['Content-Type' => 'application/json'], $data);

        $this->assertErrorResponse($response);
    }

    public function testPut()
    {
        $data     = json_encode(array('id' => 1, 'userId' => 3, 'title' => 'foobar'));
        $response = $this->sendRequest('/api', 'PUT', ['Content-Type' => 'application/json'], $data);

        $this->assertErrorResponse($response);
    }

    public function testDelete()
    {
        $data     = json_encode(array('id' => 1));
        $response = $this->sendRequest('/api', 'DELETE', ['Content-Type' => 'application/json'], $data);

        $this->assertErrorResponse($response);
    }

    public function testPatch()
    {
        $data     = json_encode(array('id' => 1, 'userId' => 3, 'title' => 'foobar'));
        $response = $this->sendRequest('/api', 'PATCH', ['Content-Type' => 'application/json'], $data);

        $this->assertErrorResponse($response);
    }

    public function testOptions()
    {
        $response = $this->sendRequest('/api', 'OPTIONS');

        $this->assertErrorResponse($response);
    }

    protected function getPaths()
    {
        return array(
            [['ANY'], '/api', NoDocumentationController::class],
        );
    }

    private function assertErrorResponse(ResponseInterface $response)
    {
        $body = (string) $response->getBody();
        $data = Parser::decode($body, true);

        $this->assertEquals(500, $response->getStatusCode());

        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('trace', $data);
        $this->assertArrayHasKey('context', $data);

        $this->assertEquals(false, $data['success']);
        $this->assertEquals('Resource is not available', substr($data['message'], 0, 25));
    }
}
