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

namespace PSX\Framework\App\Test\Tool;

use PSX\Framework\App\ApiTestCase;
use PSX\Framework\Test\Environment;

/**
 * DocumentationTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DocumentationTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('/tool/doc', 'GET');
        $baseUrl  = Environment::getBaseUrl();

        $actual = (string) $response->getBody();
        $expect = file_get_contents(__DIR__ . '/resource/documentation.json');
        $expect = str_replace('http:\/\/127.0.0.1\/', trim(json_encode($baseUrl), '"'), $expect);

        $this->assertEquals(200, $response->getStatusCode() ?: 200, $actual);
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGetDetail()
    {
        $response = $this->sendRequest('/tool/doc/*/population/popo', 'GET');
        $baseUrl  = Environment::getBaseUrl();

        $actual = (string) $response->getBody();
        $expect = file_get_contents(__DIR__ . '/resource/documentation_detail.json');
        $expect = str_replace('\/generator\/', trim(json_encode(parse_url($baseUrl, PHP_URL_PATH) . 'generator/'), '"'), $expect);

        $this->assertEquals(200, $response->getStatusCode() ?: 200, $actual);
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }
}
