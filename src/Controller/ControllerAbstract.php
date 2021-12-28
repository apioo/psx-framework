<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Api\ApiManager;
use PSX\Api\Attribute\Exclude;
use PSX\Api\ListingInterface;
use PSX\Api\Resource;
use PSX\Api\Resource\MethodAbstract;
use PSX\Api\SpecificationInterface;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Config\Config;
use PSX\Framework\Http\RequestReader;
use PSX\Framework\Http\ResponseWriter;
use PSX\Framework\Loader\Context;
use PSX\Framework\Schema\Passthru;
use PSX\Http\Environment\HttpContext;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Http\Exception as StatusCode;
use PSX\Http\Filter\CORS;
use PSX\Http\FilterChainInterface;
use PSX\Http\FilterCollectionInterface;
use PSX\Http\FilterInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Record\RecordInterface;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Schema;
use PSX\Schema\Type\ReferenceType;

/**
 * ControllerAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ControllerAbstract implements FilterInterface, FilterCollectionInterface
{
    #[Inject]
    protected Config $config;

    #[Inject]
    protected RequestReader $requestReader;

    #[Inject]
    protected ResponseWriter $responseWriter;

    #[Inject]
    protected ListingInterface $resourceListing;

    #[Inject]
    protected ApiManager $apiManager;

    protected Context $context;
    protected ?Resource $resource = null;
    protected ?DefinitionsInterface $definitions = null;
    protected ?array $allowedMethods = null;

    public function __construct(Context $context = null)
    {
        $this->context = $context ?? new Context();
    }

    /**
     * Returns a traversable of callable or FilterInterface objects
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator(array_merge(
            $this->getPreFilter(),
            [$this],
            $this->getPostFilter()
        ));
    }

    protected function getPreFilter(): array
    {
        $filter = [];

        $filter[] = new CORS(
            $this->config->get('psx_cors_origin'),
            ['OPTIONS', 'HEAD', 'GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
            $this->config->get('psx_cors_headers'),
            false
        );

        return $filter;
    }

    protected function getPostFilter(): array
    {
        return [];
    }

    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain): void
    {
        $this->initialize();

        $methodName = $request->getMethod() === 'HEAD' ? 'GET' : $request->getMethod();
        if ($methodName === 'OPTIONS') {
            $response->setHeader('Allow', implode(', ', $this->allowedMethods));
            return;
        }

        $method  = $this->getResourceMethod($methodName, $response);
        $context = $this->newContext($request);
        if (in_array($methodName, ['POST', 'PUT', 'PATCH'])) {
            $record = $this->parseRequest($request, $method);
        } else {
            $record = null;
        }

        $result = match ($methodName) {
            'GET'    => $this->doGet($context),
            'DELETE' => $this->doDelete($context),
            'POST'   => $this->doPost($record, $context),
            'PUT'    => $this->doPut($record, $context),
            'PATCH'  => $this->doPatch($record, $context),
            default  => null,
        };

        $this->responseWriter->setBody($response, $result, $request);

        $filterChain->handle($request, $response);
    }

    /**
     * Returns a http context object for the provided request
     */
    protected function newContext(RequestInterface $request): HttpContextInterface
    {
        return new HttpContext(
            $request,
            $this->context->getParameters()
        );
    }

    /**
     * Handles a GET request and returns a response
     *
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.1
     */
    #[Exclude]
    protected function doGet(HttpContextInterface $context): mixed
    {
        return null;
    }

    /**
     * Handles a POST request and returns a response
     *
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.3
     */
    #[Exclude]
    protected function doPost(mixed $record, HttpContextInterface $context): mixed
    {
        return null;
    }

    /**
     * Handles a PUT request and returns a response
     *
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.4
     */
    #[Exclude]
    protected function doPut(mixed $record, HttpContextInterface $context): mixed
    {
        return null;
    }

    /**
     * Handles a DELETE request and returns a response
     *
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.5
     */
    #[Exclude]
    protected function doDelete(HttpContextInterface $context): mixed
    {
        return null;
    }

    /**
     * Handles a PATCH request and returns a response
     *
     * @see https://tools.ietf.org/html/rfc5789#section-2
     */
    #[Exclude]
    protected function doPatch(mixed $record, HttpContextInterface $context): mixed
    {
        return null;
    }

    /**
     * Imports the request data based on the schema if available
     */
    protected function parseRequest(RequestInterface $request, MethodAbstract $method): mixed
    {
        if (!$method->hasRequest()) {
            return null;
        }

        $type   = $this->definitions->getType($method->getRequest());
        $schema = new Schema($type, $this->definitions);

        if ($type instanceof ReferenceType) {
            $passthru = $type->getRef() === Passthru::NAME;
        } else {
            $passthru = $method->getRequest() === Passthru::NAME;
        }

        if ($passthru) {
            $data = $this->requestReader->getBody($request);
        } else {
            $data = $this->requestReader->getBodyAs($request, $schema);
        }

        return $data;
    }

    private function initialize()
    {
        if ($this->resource !== null) {
            return;
        }

        $specification = $this->resourceListing->find($this->context->getPath(), $this->context->getVersion());
        if (!$specification instanceof SpecificationInterface) {
            throw new StatusCode\InternalServerErrorException('No specification available for path ' . $this->context->getPath());
        }

        $resource = $specification->getResourceCollection()->get($this->context->getPath());
        if (!$resource instanceof Resource) {
            throw new StatusCode\InternalServerErrorException('Resource is not available for path ' . $this->context->getPath());
        }

        $this->definitions = $specification->getDefinitions();
        $this->resource = $resource;
        $this->allowedMethods = $this->getAllowedMethods();
    }

    private function getResourceMethod(string $methodName, ResponseInterface $response): MethodAbstract
    {
        if (!$this->resource->hasMethod($methodName)) {
            throw new StatusCode\MethodNotAllowedException('Method is not allowed', $this->allowedMethods);
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

    private function getAllowedMethods(): array
    {
        $methods = $this->resource->getAllowedMethods();
        $allowed = ['OPTIONS'];
        if (in_array('GET', $methods)) {
            $allowed[] = 'HEAD';
        }

        return array_merge($allowed, $methods);
    }
}
