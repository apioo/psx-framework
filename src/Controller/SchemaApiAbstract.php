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

namespace PSX\Framework\Controller;

use PSX\Api\DocumentedInterface;
use PSX\Api\Resource;
use PSX\Api\Resource\MethodAbstract;
use PSX\Framework\Loader\Context;
use PSX\Framework\Schema\Passthru;
use PSX\Http\Exception as StatusCode;
use PSX\Record\Record;
use PSX\Schema\PropertyInterface;
use PSX\Schema\PropertyType;
use PSX\Schema\Schema;
use PSX\Schema\SchemaInterface;
use PSX\Schema\SchemaTraverser;

/**
 * The schema api controller helps to build an API based on a API specification
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class SchemaApiAbstract extends ApiAbstract implements DocumentedInterface
{
    /**
     * @Inject
     * @var \PSX\Api\ListingInterface
     */
    protected $resourceListing;

    /**
     * @Inject
     * @var \PSX\Api\ApiManager
     */
    protected $apiManager;

    /**
     * @Inject
     * @var \PSX\Schema\SchemaManagerInterface
     */
    protected $schemaManager;

    /**
     * @var \PSX\Record\Record
     */
    protected $queryParameters;

    /**
     * @var \PSX\Record\Record
     */
    protected $pathParameters;

    /**
     * @var \PSX\Api\Resource
     */
    protected $resource;

    public function onLoad()
    {
        parent::onLoad();

        // get the current resource and check everything is valid
        $this->resource = $this->getResource();
    }

    public function onHead()
    {
        // we support HEAD only if the resource supports a GET request since a
        // HEAD request is basically a GET without body
        if (!$this->resource->hasMethod('GET')) {
            throw new StatusCode\MethodNotAllowedException('Method is not allowed', $this->getAllowedMethods());
        }

        $method = $this->resource->getMethod('GET');

        // validate and assign query and path parameters
        $this->pathParameters  = $this->parseParameters($this->uriFragments, $this->resource->getPathParameters());
        $this->queryParameters = $this->parseParameters($this->request->getUri()->getParameters(), $method->getQueryParameters());

        $response = $this->doGet();

        // the setResponse method removes the body so we behave like on a GET 
        // request
        $this->sendResponse($method, $response);
    }

    public function onGet()
    {
        if (!$this->resource->hasMethod('GET')) {
            throw new StatusCode\MethodNotAllowedException('Method is not allowed', $this->getAllowedMethods());
        }

        $method = $this->resource->getMethod('GET');

        // validate and assign query and path parameters
        $this->pathParameters  = $this->parseParameters($this->uriFragments, $this->resource->getPathParameters());
        $this->queryParameters = $this->parseParameters($this->request->getUri()->getParameters(), $method->getQueryParameters());

        $response = $this->doGet();

        $this->sendResponse($method, $response);
    }

    public function onPost()
    {
        if (!$this->resource->hasMethod('POST')) {
            throw new StatusCode\MethodNotAllowedException('Method is not allowed', $this->getAllowedMethods());
        }

        $method = $this->resource->getMethod('POST');

        // validate and assign query and path parameters
        $this->pathParameters  = $this->parseParameters($this->uriFragments, $this->resource->getPathParameters());
        $this->queryParameters = $this->parseParameters($this->request->getUri()->getParameters(), $method->getQueryParameters());

        $record   = $this->parseRequest($method);
        $response = $this->doPost($record);

        $this->sendResponse($method, $response);
    }

    public function onPut()
    {
        if (!$this->resource->hasMethod('PUT')) {
            throw new StatusCode\MethodNotAllowedException('Method is not allowed', $this->getAllowedMethods());
        }

        $method = $this->resource->getMethod('PUT');

        // validate and assign query and path parameters
        $this->pathParameters  = $this->parseParameters($this->uriFragments, $this->resource->getPathParameters());
        $this->queryParameters = $this->parseParameters($this->request->getUri()->getParameters(), $method->getQueryParameters());

        $record   = $this->parseRequest($method);
        $response = $this->doPut($record);

        $this->sendResponse($method, $response);
    }

    public function onDelete()
    {
        if (!$this->resource->hasMethod('DELETE')) {
            throw new StatusCode\MethodNotAllowedException('Method is not allowed', $this->getAllowedMethods());
        }

        $method = $this->resource->getMethod('DELETE');

        // validate and assign query and path parameters
        $this->pathParameters  = $this->parseParameters($this->uriFragments, $this->resource->getPathParameters());
        $this->queryParameters = $this->parseParameters($this->request->getUri()->getParameters(), $method->getQueryParameters());

        $record   = $this->parseRequest($method);
        $response = $this->doDelete($record);

        $this->sendResponse($method, $response);
    }

    public function onPatch()
    {
        if (!$this->resource->hasMethod('PATCH')) {
            throw new StatusCode\MethodNotAllowedException('Method is not allowed', $this->getAllowedMethods());
        }

        $method = $this->resource->getMethod('PATCH');

        // validate and assign query and path parameters
        $this->pathParameters  = $this->parseParameters($this->uriFragments, $this->resource->getPathParameters());
        $this->queryParameters = $this->parseParameters($this->request->getUri()->getParameters(), $method->getQueryParameters());

        $record   = $this->parseRequest($method);
        $response = $this->doPatch($record);

        $this->sendResponse($method, $response);
    }

    public function onOptions()
    {
        $this->setHeader('Allow', implode(', ', $this->getAllowedMethods()));
        $this->setResponseCode(200);
        $this->setBody('');
    }

    public function getDocumentation($version = null)
    {
        return $this->apiManager->getApi(get_class($this), $this->context->get(Context::KEY_PATH));
    }

    /**
     * Handles a GET request and returns a response
     *
     * @Exclude
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.1
     * @return mixed
     */
    protected function doGet()
    {
    }

    /**
     * Handles a POST request and returns a response
     *
     * @Exclude
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.3
     * @param mixed $record
     * @return mixed
     */
    protected function doPost($record)
    {
    }

    /**
     * Handles a PUT request and returns a response
     *
     * @Exclude
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.4
     * @param mixed $record
     * @return mixed
     */
    protected function doPut($record)
    {
    }

    /**
     * Handles a DELETE request and returns a response
     *
     * @Exclude
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.5
     * @param mixed $record
     * @return mixed
     */
    protected function doDelete($record)
    {
    }

    /**
     * Handles a PATCH request and returns a response
     *
     * @Exclude
     * @see https://tools.ietf.org/html/rfc5789#section-2
     * @param mixed $record
     * @return mixed
     */
    protected function doPatch($record)
    {
    }

    /**
     * Imports the request data based on the schema if available
     *
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @return \PSX\Record\RecordInterface
     */
    protected function parseRequest(MethodAbstract $method)
    {
        if ($method->hasRequest()) {
            $schema = $method->getRequest();
            if ($schema instanceof Passthru) {
                $data = $this->getBody();
            } elseif ($schema instanceof SchemaInterface) {
                $data = $this->getBodyAs($method->getRequest(), $this->getValidator($method));
            } else {
                $data = new Record();
            }
        } else {
            $data = null;
        }

        return $data;
    }

    /**
     * Returns the validator which is used for a specific request method
     *
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @return \PSX\Schema\ValidatorInterface
     */
    protected function getValidator(MethodAbstract $method)
    {
        return null;
    }

    /**
     * Checks whether a response schema is defined for the provided status code
     * and writes the data to the body if a status code is available. Otherwise 
     * the API returns 204 no content
     *
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @param mixed $response
     */
    protected function sendResponse(MethodAbstract $method, $response)
    {
        $statusCode = $this->response->getStatusCode();
        if (empty($statusCode)) {
            // in case we have only one defined response use this code
            $responses = $method->getResponses();
            if (count($responses) == 1) {
                $statusCode = key($responses);
            } else {
                $statusCode = 200;
            }
        }

        if ($method->hasResponse($statusCode)) {
            $this->setResponseCode($statusCode);
            $this->setBody($response);
        } else {
            $this->setResponseCode(204);
            $this->setBody('');
        }
    }

    /**
     * Returns the resource from the listing for the current path
     *
     * @return \PSX\Api\Resource
     */
    protected function getResource()
    {
        $resource = $this->resourceListing->getResource($this->context->get(Context::KEY_PATH), $this->context->get(Context::KEY_VERSION));

        if (!$resource instanceof Resource) {
            throw new StatusCode\InternalServerErrorException('Resource is not available');
        }

        if ($resource->isActive()) {
        } elseif ($resource->isDeprecated()) {
            $this->response->addHeader('Warning', '199 PSX "Resource is deprecated"');
        } elseif ($resource->isClosed()) {
            throw new StatusCode\GoneException('Resource is not longer supported');
        } elseif ($resource->isDevelopment()) {
            $this->response->addHeader('Warning', '199 PSX "Resource is in development"');
        }

        return $resource;
    }

    /**
     * @param array $data
     * @param \PSX\Schema\PropertyInterface $property
     * @return mixed
     */
    private function parseParameters(array $data, PropertyInterface $property)
    {
        $traverser = new SchemaTraverser();

        $data = $traverser->traverse(
            $this->convertParameterTypes($data, $property),
            new Schema($property)
        );

        return Record::fromStdClass($data);
    }

    /**
     * @param array $parameters
     * @param \PSX\Schema\PropertyInterface $property
     * @return \stdClass
     */
    private function convertParameterTypes(array $parameters, PropertyInterface $property)
    {
        $data = new \stdClass();
        $keys = [];

        $properties = $property->getProperties();
        if (!empty($properties)) {
            foreach ($properties as $name => $property) {
                if (isset($parameters[$name])) {
                    $data->{$name} = $this->convertPropertyType($parameters[$name], $property);

                    $keys[] = $name;
                }
            }
        }

        $additionalProperties = $property->getAdditionalProperties();
        if ($additionalProperties === true) {
            $diff = array_diff(array_keys($parameters), $keys);
            foreach ($diff as $name) {
                $data->{$name} = $parameters[$name];
            }
        } elseif ($additionalProperties instanceof PropertyInterface) {
            $diff = array_diff(array_keys($parameters), $keys);
            foreach ($diff as $name) {
                $this->convertPropertyType($parameters[$name], $additionalProperties);
            }
        }

        return $data;
    }

    /**
     * @param mixed $data
     * @param \PSX\Schema\PropertyInterface $property
     * @return mixed
     */
    private function convertPropertyType($data, PropertyInterface $property)
    {
        if ($property->getType() === PropertyType::TYPE_INTEGER) {
            return (int) $data;
        } elseif ($property->getType() === PropertyType::TYPE_NUMBER) {
            return (float) $data;
        } elseif ($property->getType() === PropertyType::TYPE_BOOLEAN) {
            return (bool) $data;
        } elseif ($property->getType() === PropertyType::TYPE_STRING) {
            return (string) $data;
        } elseif ($property->getType() === PropertyType::TYPE_NULL) {
            return null;
        } else {
            return $data;
        }
    }

    /**
     * @return array
     */
    private function getAllowedMethods()
    {
        $methods = $this->resource->getAllowedMethods();
        $allowed = ['OPTIONS'];
        if (in_array('GET', $methods)) {
            $allowed[] = 'HEAD';
        }

        return array_merge(
            $allowed,
            $methods
        );
    }
}
