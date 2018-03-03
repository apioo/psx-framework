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

namespace PSX\Framework\App;

use GuzzleHttp\Client;
use PSX\Framework\Test\ControllerDbTestCase;
use PSX\Framework\App\Api\Population;
use PSX\Framework\Controller\Generator;
use PSX\Framework\Controller\Tool;
use PSX\Framework\Test\Environment;
use PSX\Http\Response;

/**
 * ApiTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiTestCase extends ControllerDbTestCase
{
    /**
     * @var \GuzzleHttp\Client
     */
    private static $httpClient;

    /**
     * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/api_fixture.xml');
    }

    /**
     * @return array
     */
    protected function getPaths()
    {
        return [
            [['ANY'], '/population/popo', Population\CollectionPopo::class],
            [['ANY'], '/population/popo/:id', Population\EntityPopo::class],
            [['ANY'], '/population/jsonschema', Population\CollectionJsonSchema::class],
            [['ANY'], '/population/jsonschema/:id', Population\EntityJsonSchema::class],
            [['ANY'], '/population/raml', Api\Population\CollectionRaml::class],
            [['ANY'], '/population/raml/:id', Population\EntityRaml::class],
            [['ANY'], '/population/openapi', Population\CollectionOpenAPI::class],
            [['ANY'], '/population/openapi/:id', Population\EntityOpenAPI::class],
            [['ANY'], '/population', Population\Collection::class],
            [['ANY'], '/population/:id', Population\Entity::class],

            [['GET'], '/tool', Tool\DefaultController::class],
            [['GET'], '/tool/discovery', Tool\DiscoveryController::class],
            [['GET'], '/tool/doc', Tool\Documentation\IndexController::class],
            [['GET'], '/tool/doc/:version/*path', Tool\Documentation\DetailController::class],
            [['GET'], '/tool/routing', Tool\RoutingController::class],

            [['GET'], '/generator/raml/:version/*path', Generator\RamlController::class],
            [['GET'], '/generator/swagger/:version/*path', Generator\SwaggerController::class],
            [['GET'], '/generator/openapi/:version/*path', Generator\OpenAPIController::class],
        ];
    }

    /**
     * Send a request either internal or through an actual HTTP request
     *
     * @param string $uri
     * @param string $method
     * @param array $headers
     * @param null $body
     * @return \PSX\Http\ResponseInterface
     */
    protected function sendRequest($uri, $method, $headers = array(), $body = null)
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

    /**
     * @return \GuzzleHttp\Client
     */
    private static function getHttpClient()
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
