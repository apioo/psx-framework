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

namespace PSX\Framework\Controller\Tool\Documentation;

use PSX\Api\Generator;
use PSX\Api\Resource;
use PSX\Framework\Controller\Generator\OpenAPIController;
use PSX\Framework\Controller\Generator\RamlController;
use PSX\Framework\Controller\Generator\SwaggerController;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Schema;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Http\Exception as StatusCode;

/**
 * DetailController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DetailController extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Api\ListingInterface
     */
    protected $resourceListing;

    /**
     * @Inject
     * @var \PSX\Api\Listing\FilterFactoryInterface
     */
    protected $listingFilterFactory;

    /**
     * @Inject
     * @var \PSX\Framework\Loader\ReverseRouter
     */
    protected $reverseRouter;

    /**
     * @inheritdoc
     */
    public function getDocumentation($version = null)
    {
        $resource = new Resource(Resource::STATUS_ACTIVE, $this->context->getPath());

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->addResponse(200, $this->schemaManager->getSchema(Schema\Documentation\Detail::class))
        );

        return $resource;
    }

    public function doGet(HttpContextInterface $httpContext)
    {
        $version = $this->context->getParameter('version');
        $path    = $this->context->getParameter('path') ?: '/';

        if (empty($version) || empty($path)) {
            throw new StatusCode\BadRequestException('Version and path not provided');
        }

        $resource = $this->resourceListing->getResource($path, $version);

        if (!$resource instanceof Resource) {
            throw new StatusCode\BadRequestException('Invalid api version');
        }

        $generator = new Generator\JsonSchema($this->config['psx_json_namespace']);

        $api = new \stdClass();
        $api->path = $resource->getPath();
        $api->version = $version;
        $api->status = $resource->getStatus();
        $api->description = $resource->getDescription();
        $api->schema = $generator->toArray($resource);

        // path parameters
        if ($resource->hasPathParameters()) {
            $api->pathParameters = '#/definitions/path-template';
        }

        // methods
        $methods = $resource->getMethods();
        $details = [];

        foreach ($methods as $method) {
            $data = new \stdClass();

            // description
            $description = $method->getDescription();
            if (!empty($description)) {
                $data->description = $description;
            }

            // query parameters
            if ($method->hasQueryParameters()) {
                $data->queryParameters = '#/definitions/' . $method->getName() . '-query';
            }

            // request
            if ($method->hasRequest()) {
                $data->request = '#/definitions/' . $method->getName() . '-request';
            }

            // responses
            $responses = $method->getResponses();
            if (!empty($responses)) {
                $resps = array();
                foreach ($responses as $statusCode => $resp) {
                    $resps[$statusCode] = '#/definitions/' . $method->getName() . '-' . $statusCode . '-response';
                }

                $data->responses = $resps;
            }

            $details[$method->getName()] = $data;
        }

        $api->methods = $details;

        // links
        $links = $this->getLinks($version, $resource->getPath());
        if (!empty($links)) {
            $api->links = $links;
        }

        return $api;
    }

    protected function getLinks($version, $path)
    {
        $path   = ltrim($path, '/');
        $result = [];

        $openAPIPath = $this->reverseRouter->getAbsolutePath(OpenAPIController::class, array('version' => $version, 'path' => $path));
        if ($openAPIPath !== null) {
            $result[] = [
                'rel'  => 'openapi',
                'href' => $openAPIPath,
            ];
        }

        $swaggerPath = $this->reverseRouter->getAbsolutePath(SwaggerController::class, array('version' => $version, 'path' => $path));
        if ($swaggerPath !== null) {
            $result[] = [
                'rel'  => 'swagger',
                'href' => $swaggerPath,
            ];
        }

        $ramlPath = $this->reverseRouter->getAbsolutePath(RamlController::class, array('version' => $version, 'path' => $path));
        if ($ramlPath !== null) {
            $result[] = [
                'rel'  => 'raml',
                'href' => $ramlPath,
            ];
        }

        return $result;
    }
}
