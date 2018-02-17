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

use PSX\Data\Writer;
use PSX\Data\WriterInterface;
use PSX\Framework\ApplicationStackInterface;
use PSX\Framework\Http\WriterOptions;
use PSX\Framework\Loader\Context;
use PSX\Http\Exception as StatusCode;
use PSX\Http\FilterChainInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * ControllerAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ControllerAbstract implements ControllerInterface, ApplicationStackInterface
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
     * @var \PSX\Framework\Loader\Context
     */
    protected $context;

    /**
     * @var array
     * @deprecated
     */
    protected $uriFragments;

    /**
     * @Inject
     * @var \PSX\Framework\Config\Config
     */
    protected $config;

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
        $this->uriFragments = $context->getParameters();
    }

    /**
     * @inheritdoc
     */
    public function getApplicationStack()
    {
        $controller = function(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain){
            $this->setState($request, $response);

            $this->onLoad();
            $this->onRequest($request, $response);
            $this->onFinish();

            $filterChain->handle($request, $response);
        };

        return array_merge(
            $this->getPreFilter(),
            [$controller],
            $this->getPostFilter()
        );
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
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     * @internal
     */
    public function setState(RequestInterface $request, ResponseInterface $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    /**
     * @inheritdoc
     */
    public function onLoad()
    {
        // we change the supported writer only if available
        $supportedWriter = $this->getSupportedWriter();
        if (!empty($supportedWriter)) {
            $this->context->setSupportedWriter($supportedWriter);
        }
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function onFinish()
    {
        $this->request  = null;
        $this->response = null;
    }

    /**
     * @inheritdoc
     */
    public function onGet(RequestInterface $request, ResponseInterface $response)
    {
    }

    /**
     * @inheritdoc
     */
    public function onHead(RequestInterface $request, ResponseInterface $response)
    {
    }

    /**
     * @inheritdoc
     */
    public function onPost(RequestInterface $request, ResponseInterface $response)
    {
    }

    /**
     * @inheritdoc
     */
    public function onPut(RequestInterface $request, ResponseInterface $response)
    {
    }

    /**
     * @inheritdoc
     */
    public function onDelete(RequestInterface $request, ResponseInterface $response)
    {
    }

    /**
     * @inheritdoc
     */
    public function onOptions(RequestInterface $request, ResponseInterface $response)
    {
    }

    /**
     * @inheritdoc
     */
    public function onPatch(RequestInterface $request, ResponseInterface $response)
    {
    }

    /**
     * Returns a specific uri fragment
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
     * Can be overridden by a controller to return the formats which are
     * supported. All following controllers will have the same supported writers
     * as the origin controller. If null gets returned every available format is
     * supported otherwise it must return an array containing writer class names
     *
     * @return array
     */
    protected function getSupportedWriter()
    {
        return null;
    }

    /**
     * Returns the writer options for the provided request and the current 
     * context of the controller
     * 
     * @param \PSX\Http\RequestInterface $request
     * @return \PSX\Framework\Http\WriterOptions
     */
    protected function getWriterOptions(RequestInterface $request, $writerType = null)
    {
        $options = new WriterOptions();
        $options->setWriterType($writerType);
        $options->setContentType($request->getHeader('Accept'));
        $options->setFormat($request->getUri()->getParameter('format'));
        $options->setSupportedWriter($this->context->getSupportedWriter());
        $options->setRequestMethod($request->getMethod());
        $options->setWriterCallback(function(WriterInterface $writer) use ($request){
            if ($writer instanceof Writer\Soap) {
                if (!$writer->getRequestMethod()) {
                    $writer->setRequestMethod($request->getMethod());
                }
            } elseif ($writer instanceof Writer\Jsonp) {
                if (!$writer->getCallbackName()) {
                    $writer->setCallbackName($request->getUri()->getParameter('callback'));
                }
            }
        });

        return $options;
    }
}
