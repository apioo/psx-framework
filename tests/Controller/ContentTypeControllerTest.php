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

namespace Controller;

use PSX\Framework\Test\ControllerDbTestCase;
use PSX\Http\Http;
use PSX\Http\Stream\MultipartStream;
use PSX\Http\Stream\StringStream;

/**
 * SchemaControllerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ContentTypeControllerTest extends ControllerDbTestCase
{
    public function getDataSet(): array
    {
        return $this->createFromFile(__DIR__ . '/../table_fixture.php');
    }

    public function testBinary()
    {
        $response = $this->sendRequest('/tests/content_type/binary', 'POST', ['Content-Type' => 'application/octet-stream'], new StringStream('foobar'));
        $body     = (string) $response->getBody();
        $expect   = 'foobar';

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals('application/octet-stream', $response->getHeader('Content-Type'));
        $this->assertEquals($expect, $body, $body);
    }

    public function testForm()
    {
        $response = $this->sendRequest('/tests/content_type/form', 'POST', ['Content-Type' => 'application/x-www-form-urlencoded'], new StringStream('foo=bar'));
        $body     = (string) $response->getBody();
        $expect   = 'foo=bar';

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals('application/x-www-form-urlencoded', $response->getHeader('Content-Type'));
        $this->assertEquals($expect, $body, $body);
    }

    public function testJson()
    {
        $response = $this->sendRequest('/tests/content_type/json', 'POST', ['Content-Type' => 'application/json'], new StringStream(\json_encode(['foo' => 'bar'])));
        $body     = (string) $response->getBody();
        $expect   = ['foo' => 'bar'];

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $this->assertJsonStringEqualsJsonString(\json_encode($expect), $body, $body);
    }

    public function testMultipart()
    {
        $this->markTestSkipped('Currently works only on single execution since $_FILES is modified');

        $fileName = __DIR__ . '/resource/tmp_file.txt';
        file_put_contents($fileName, 'foobar');

        $_FILES['file'] = [
            'name' => 'file.txt',
            'tmp_name' => $fileName,
            'type' => 'text/plain',
            'size' => filesize($fileName),
            'error' => 0,
        ];

        $response = $this->sendRequest('/tests/content_type/multipart', 'POST', ['Content-Type' => 'multipart/form-data'], new StringStream('foo=bar'));
        $body     = (string) $response->getBody();

        $contentType = $response->getHeader('Content-Type');
        preg_match('~^multipart/form-data; boundary="([0-9A-Fa-f]{40})"$~', $contentType, $matches);
        $boundary = $matches[1] ?? null;
        $this->assertNotEmpty($boundary, $body);

        $expect = '--' . $boundary . Http::NEW_LINE;
        $expect.= 'content-type: text/plain' . Http::NEW_LINE;
        $expect.= 'content-disposition: form-data; name="file"; filename="file.txt"' . Http::NEW_LINE;
        $expect.= Http::NEW_LINE;
        $expect.= 'foobar';
        $expect.= Http::NEW_LINE;
        $expect.= '--' . $boundary . '--' . Http::NEW_LINE;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals($expect, $body, $body);
    }

    public function testText()
    {
        $response = $this->sendRequest('/tests/content_type/text', 'POST', ['Content-Type' => 'text/plain'], new StringStream('foobar'));
        $body     = (string) $response->getBody();
        $expect   = 'foobar';

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals($expect, $body, $body);
    }
}
