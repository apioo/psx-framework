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

namespace PSX\Framework\Controller;

use PSX\Api\DocumentedInterface;
use PSX\Api\Resource;
use PSX\Api\Resource\MethodAbstract;
use PSX\Framework\Schema\Passthru;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Http\Exception as StatusCode;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\StringStream;
use PSX\Record\Record;
use PSX\Schema\SchemaInterface;

/**
 * The schema api controller helps to build an API based on a API specification.
 * The controller knows the schema of the resource
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class SchemaApiAbstract extends ControllerAbstract implements DocumentedInterface
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
     * @var \PSX\Api\Resource
     */
    protected $resource;

    public function onLoad()
    {
        parent::onLoad();

        // get the current resource based on the context
        $this->resource = $this->getResource();
    }

    /**
     * @inheritdoc
     */
    public function onHead(RequestInterface $request, ResponseInterface $response)
    {
        $method  = $this->getResourceMethod('GET', $response);
        $context = $this->newContext($request);
        $result  = $this->doGet($context);

        $this->sendResponse($method, $request, $response, $result);
    }

    /**
     * @inheritdoc
     */
    public function onGet(RequestInterface $request, ResponseInterface $response)
    {
        $method  = $this->getResourceMethod('GET', $response);
        $context = $this->newContext($request);
        $result  = $this->doGet($context);

        $this->sendResponse($method, $request, $response, $result);
    }

    /**
     * @inheritdoc
     */
    public function onPost(RequestInterface $request, ResponseInterface $response)
    {
        $method  = $this->getResourceMethod('POST', $response);
        $context = $this->newContext($request);
        $record  = $this->parseRequest($request, $method);
        $result  = $this->doPost($record, $context);

        $this->sendResponse($method, $request, $response, $result);
    }

    /**
     * @inheritdoc
     */
    public function onPut(RequestInterface $request, ResponseInterface $response)
    {
        $method  = $this->getResourceMethod('PUT', $response);
        $context = $this->newContext($request);
        $record  = $this->parseRequest($request, $method);
        $result  = $this->doPut($record, $context);

        $this->sendResponse($method, $request, $response, $result);
    }

    /**
     * @inheritdoc
     */
    public function onDelete(RequestInterface $request, ResponseInterface $response)
    {
        $method  = $this->getResourceMethod('DELETE', $response);
        $context = $this->newContext($request);
        $record  = $this->parseRequest($request, $method);
        $result  = $this->doDelete($record, $context);

        $this->sendResponse($method, $request, $response, $result);
    }

    /**
     * @inheritdoc
     */
    public function onPatch(RequestInterface $request, ResponseInterface $response)
    {
        $method  = $this->getResourceMethod('PATCH', $response);
        $context = $this->newContext($request);
        $record  = $this->parseRequest($request, $method);
        $result  = $this->doPatch($record, $context);

        $this->sendResponse($method, $request, $response, $result);
    }

    /**
     * @inheritdoc
     */
    public function onOptions(RequestInterface $request, ResponseInterface $response)
    {
        $methods = $this->getAllowedMethods();

        $this->corsPolicy->handle($request, $response, $methods);

        $response->setHeader('Allow', implode(', ', $methods));
        $response->setStatus(200);
        $response->setBody(new StringStream(''));
    }

    /**
     * @inheritdoc
     */
    public function getDocumentation($version = null)
    {
        return $this->apiManager->getApi(get_class($this), $this->context->getPath());
    }

    /**
     * Handles a GET request and returns a response
     *
     * @Exclude
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.1
     * @param \PSX\Http\Environment\HttpContextInterface $context
     * @return mixed
     */
    protected function doGet(HttpContextInterface $context)
    {
    }

    /**
     * Handles a POST request and returns a response
     *
     * @Exclude
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.3
     * @param mixed $record
     * @param \PSX\Http\Environment\HttpContextInterface $context
     * @return mixed
     */
    protected function doPost($record, HttpContextInterface $context)
    {
    }

    /**
     * Handles a PUT request and returns a response
     *
     * @Exclude
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.4
     * @param mixed $record
     * @param \PSX\Http\Environment\HttpContextInterface $context
     * @return mixed
     */
    protected function doPut($record, HttpContextInterface $context)
    {
    }

    /**
     * Handles a DELETE request and returns a response
     *
     * @Exclude
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.5
     * @param mixed $record
     * @param \PSX\Http\Environment\HttpContextInterface $context
     * @return mixed
     */
    protected function doDelete($record, HttpContextInterface $context)
    {
    }

    /**
     * Handles a PATCH request and returns a response
     *
     * @Exclude
     * @see https://tools.ietf.org/html/rfc5789#section-2
     * @param mixed $record
     * @param \PSX\Http\Environment\HttpContextInterface $context
     * @return mixed
     */
    protected function doPatch($record, HttpContextInterface $context)
    {
    }

    /**
     * Imports the request data based on the schema if available
     *
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @return \PSX\Record\RecordInterface
     */
    protected function parseRequest(RequestInterface $request, MethodAbstract $method)
    {
        if ($method->hasRequest()) {
            $schema = $method->getRequest();
            if ($schema instanceof Passthru) {
                $data = $this->requestReader->getBody($request);
            } elseif ($schema instanceof SchemaInterface) {
                $data = $this->requestReader->getBodyAs($request, $method->getRequest(), $this->getValidator($method));
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
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     * @param mixed $data
     */
    private function sendResponse(MethodAbstract $method, RequestInterface $request, ResponseInterface $response, $data)
    {
        $statusCode = $response->getStatusCode();
        if (empty($statusCode)) {
            // in case we have only one defined response use this code
            $responses = $method->getResponses();
            if (count($responses) == 1) {
                $statusCode = key($responses);
            } else {
                $statusCode = 200;
            }

            $response->setStatus($statusCode);
        }

        $this->responseWriter->setBody($response, $data, $request);
    }

    /**
     * Returns the resource from the listing for the current path
     *
     * @return \PSX\Api\Resource
     */
    private function getResource()
    {
        $resource = $this->resourceListing->getResource($this->context->getPath(), $this->context->getVersion());

        if (!$resource instanceof Resource) {
            throw new StatusCode\InternalServerErrorException('Resource is not available');
        }

        return $resource;
    }

    /**
     * @param string $methodName
     * @param \PSX\Http\ResponseInterface $response
     * @return \PSX\Api\Resource\MethodAbstract
     */
    private function getResourceMethod($methodName, ResponseInterface $response)
    {
        if (!$this->resource->hasMethod($methodName)) {
            throw new StatusCode\MethodNotAllowedException('Method is not allowed', $this->getAllowedMethods());
        }

        if ($this->resource->isActive()) {
        } elseif ($this->resource->isDeprecated()) {
            $response->addHeader('Warning', '199 PSX "Resource is deprecated"');
        } elseif ($this->resource->isClosed()) {
            throw new StatusCode\GoneException('Resource is not longer supported');
        } elseif ($this->resource->isDevelopment()) {
            $response->addHeader('Warning', '199 PSX "Resource is in development"');
        }

        return $this->resource->getMethod($methodName);
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
