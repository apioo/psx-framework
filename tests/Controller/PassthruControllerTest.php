<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Tests\Controller;

use PSX\Framework\Test\ControllerTestCase;

/**
 * PassthruControllerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PassthruControllerTest extends ControllerTestCase
{
    private array $payload;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->payload = [
            'any' => [
                'foo' => 'bar'
            ],
            'array' => ['bar'],
            'arrayComplex' => [[
                'foo' => 'bar'
            ],[
                'foo' => 'foo'
            ]],
            'arrayChoice' => [[
                'foo' => 'baz'
            ],[
                'bar' => 'bar'
            ],[
                'foo' => 'foo'
            ]],
            'boolean' => true,
            'choice' => [
                'bar' => 'test'
            ],
            'complex' => [
                'foo' => 'bar'
            ],
            'date' => '2015-05-01',
            'dateTime' => '2015-05-01T13:37:14Z',
            'duration' => 'P1M',
            'float' => 13.37,
            'integer' => 7,
            'string' => 'bar',
            'time' => '13:37:14',
        ];
    }

    public function testGet()
    {
        $response = $this->sendRequest('/tests/passthru', 'GET');
        $body     = (string) $response->getBody();

        $this->assertJsonStringEqualsJsonString(json_encode($this->payload), $body, $body);
    }
    
    public function testPost()
    {
        $response = $this->sendRequest('/tests/passthru', 'POST', ['Content-Type' => 'application/json'], json_encode($this->payload));
        $body     = (string) $response->getBody();

        $this->assertJsonStringEqualsJsonString(json_encode($this->payload), $body, $body);
    }
}
