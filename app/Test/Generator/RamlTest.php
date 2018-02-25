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

namespace PSX\Framework\App\Test\Generator;

use PSX\Framework\App\ApiTestCase;
use PSX\Framework\Test\Environment;
use Symfony\Component\Yaml\Yaml;

/**
 * RamlTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RamlTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('/generator/raml/*/population/popo', 'GET');
        $baseUrl  = Environment::getBaseUrl();

        $actual = (string) $response->getBody();
        $expect = file_get_contents(__DIR__ . '/resource/raml.yaml');
        $expect = str_replace('http://127.0.0.1/', $baseUrl, $expect);

        $this->assertEquals(200, $response->getStatusCode() ?: 200, $actual);
        $this->assertEquals(Yaml::parse($expect), Yaml::parse($actual), $actual);
    }

    public function testGetCollection()
    {
        $response = $this->sendRequest('/generator/raml/*/*', 'GET');
        $baseUrl  = Environment::getBaseUrl();

        $actual = (string) $response->getBody();
        $expect = file_get_contents(__DIR__ . '/resource/raml_collection.yaml');
        $expect = str_replace('http://127.0.0.1/', $baseUrl, $expect);

        $this->assertEquals(200, $response->getStatusCode() ?: 200, $actual);
        $this->assertEquals(Yaml::parse($expect), Yaml::parse($actual), $actual);
    }
}
