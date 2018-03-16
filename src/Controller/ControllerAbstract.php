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

use PSX\Framework\Loader\Context;
use PSX\Http\Environment\HttpContext;
use PSX\Http\Exception as StatusCode;
use PSX\Http\FilterChainInterface;
use PSX\Http\FilterCollectionInterface;
use PSX\Http\FilterInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * ControllerAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ControllerAbstract implements FilterInterface, FilterCollectionInterface
{
    use Behaviour\HttpTrait;
    use Behaviour\RedirectTrait;

    /**
     * @var \PSX\Http\RequestInterface
     * @deprecated
     */
    protected $request;

    /**
     * @var \PSX\Http\ResponseInterface
     * @deprecated
     */
    protected $response;

    /**
     * @var array
     * @deprecated
     */
    protected $uriFragments;

    /**
     * @var \PSX\Framework\Loader\Context
     */
    protected $context;

    /**
     * @Inject
     * @var \PSX\Framework\Config\Config
     */
    protected $config;

    /**
     * @Inject
     * @var \PSX\Framework\Http\CorsPolicy
     */
    protected $corsPolicy;

    /**
     * @Inject
     * @var \PSX\Framework\Http\RequestReader
     */
    protected $requestReader;

    /**
     * @Inject
     * @var \PSX\Framework\Http\ResponseWriter
     */
    protected $responseWriter;

    /**
     * @param \PSX\Framework\Loader\Context $context
     */
    public function __construct(Context $context = null)
    {
        $this->context      = $context ?? new Context();
        $this->uriFragments = $this->context->getParameters();
    }

    /**
     * Returns a traversable of callable or FilterInterface objects
     * 
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_merge(
            $this->getPreFilter(),
            [$this],
            $this->getPostFilter()
        ));
    }

    /**
     * @return array
     */
    public function getPreFilter()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getPostFilter()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
    {
        $this->setState($request, $response);

        $this->onLoad();
        $this->onRequest($request, $response);
        $this->onFinish();

        $filterChain->handle($request, $response);
    }

    /**
     * Is called on load to initialize state which does not depend on the 
     * request and response context. It is recommended to use this method 
     * instead of the constructor
     */
    public function onLoad()
    {
    }

    /**
     * Is called if a request arrives at our controller. The controller can read
     * data from the request object and write data to the response body
     * 
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     */
    public function onRequest(RequestInterface $request, ResponseInterface $response)
    {
        switch ($request->getMethod()) {
            case 'GET':
                $this->onGet($request, $response);
                break;

            case 'HEAD':
                $this->onHead($request, $response);
                break;

            case 'POST':
                $this->onPost($request, $response);
                break;

            case 'PUT':
                $this->onPut($request, $response);
                break;

            case 'DELETE':
                $this->onDelete($request, $response);
                break;

            case 'OPTIONS':
                $this->onOptions($request, $response);
                break;

            case 'PATCH':
                $this->onPatch($request, $response);
                break;

            default:
                throw new StatusCode\NotImplementedException('Request method is not supported');
                break;
        }
    }

    /**
     * Is called after the request to potentially clean up state
     */
    public function onFinish()
    {
        $this->request  = null;
        $this->response = null;
    }

    /**
     * Is called if the client has send a GET request 
     * 
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.1
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     */
    public function onGet(RequestInterface $request, ResponseInterface $response)
    {
    }

    /**
     * Is called if the client has send a HEAD request. Note the framework 
     * automatically removes the response body
     * 
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.2
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     */
    public function onHead(RequestInterface $request, ResponseInterface $response)
    {
    }

    /**
     * Is called if the client has send a POST request
     * 
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.3
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     */
    public function onPost(RequestInterface $request, ResponseInterface $response)
    {
    }

    /**
     * Is called if the client has send a PUT request
     * 
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.4
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     */
    public function onPut(RequestInterface $request, ResponseInterface $response)
    {
    }

    /**
     * Is called if the client has send a DELETE request
     * 
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.5
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     */
    public function onDelete(RequestInterface $request, ResponseInterface $response)
    {
    }

    /**
     * Is called if the client has send a OPTIONS request
     * 
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.7
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     */
    public function onOptions(RequestInterface $request, ResponseInterface $response)
    {
    }

    /**
     * Is called if the client has send a PATCH request
     * 
     * @see https://tools.ietf.org/html/rfc5789#section-2
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     */
    public function onPatch(RequestInterface $request, ResponseInterface $response)
    {
    }

    /**
     * Returns a specific uri fragment. This method is deprecated please access
     * the uri fragment directly through $this->context->getParameter()
     *
     * @param string $key
     * @return string
     * @deprecated
     */
    protected function getUriFragment($key)
    {
        return isset($this->uriFragments[$key]) ? $this->uriFragments[$key] : null;
    }

    /**
     * Returns a http context object for the provided request
     * 
     * @param \PSX\Http\RequestInterface $request
     * @return \PSX\Http\Environment\HttpContextInterface
     */
    protected function newContext(RequestInterface $request)
    {
        return new HttpContext(
            $request,
            $this->context->getParameters()
        );
    }

    /**
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     */
    private function setState(RequestInterface $request, ResponseInterface $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }
}
