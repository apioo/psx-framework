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

namespace PSX\Framework\App;

use GuzzleHttp\Client;
use PSX\Framework\Test\ControllerDbTestCase;
use PSX\Framework\Test\Environment;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;
use PSX\Uri\Uri;

/**
 * ApiTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiTestCase extends ControllerDbTestCase
{
    private static ?Client $httpClient = null;

    public function getDataSet(): array
    {
        return $this->createFromFile(__DIR__ . '/api_fixture.php');
    }

    /**
     * Send a request either internal or through an actual HTTP request
     */
    protected function sendRequest(string|Uri $uri, string $method, array $headers = [], ?string $body = null): ResponseInterface
    {
        if (getenv('SEND') == 'external') {
            $response = self::getHttpClient()->request($method, ltrim($uri, '/'), [
                'headers' => $headers,
                'body'    => $body,
            ]);

            return new Response($response->getStatusCode(), $response->getHeaders(), $response->getBody());
        } else {
            return parent::sendRequest($uri, $method, $headers, $body);
        }
    }

    private static function getHttpClient(): Client
    {
        if (self::$httpClient) {
            return self::$httpClient;
        }

        return self::$httpClient = new Client([
            'base_uri'    => Environment::getBaseUrl(),
            'http_errors' => false,
        ]);
    }
}
