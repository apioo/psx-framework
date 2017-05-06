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

use DOMDocument;
use PSX\Data\Accessor;
use PSX\Data\Payload;
use PSX\Data\TransformerInterface;
use PSX\Framework\ApplicationStackInterface;
use PSX\Framework\Controller\Behaviour;
use PSX\Framework\Data\Writer as FrameworkWriter;
use PSX\Framework\Filter\ControllerExecutor;
use PSX\Framework\Loader\Context;
use PSX\Data\ReaderInterface;
use PSX\Record\Record;
use PSX\Record\RecordInterface;
use PSX\Data\Writer;
use PSX\Data\WriterInterface;
use PSX\Http\Exception as StatusCode;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Http\StreamInterface;
use PSX\Schema\Validation\ValidatorInterface;
use PSX\Schema\Validation\Validator;
use PSX\Schema\Visitor\TypeVisitor;
use ReflectionClass;
use SimpleXMLElement;

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
     */
    protected $request;

    /**
     * @var \PSX\Http\ResponseInterface
     */
    protected $response;

    /**
     * @var \PSX\Framework\Loader\Context
     */
    protected $context;

    /**
     * @var array
     */
    protected $uriFragments;

    /**
     * @Inject
     * @var \PSX\Framework\Config\Config
     */
    protected $config;

    /**
     * @Inject
     * @var \PSX\Validate\Validate
     */
    protected $validate;

    /**
     * @Inject
     * @var \PSX\Data\Processor
     */
    protected $io;

    private $_responseWritten = false;

    /**
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     * @param \PSX\Framework\Loader\Context $context
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, Context $context = null)
    {
        $this->request      = $request;
        $this->response     = $response;
        $this->context      = $context ?: new Context();
        $this->uriFragments = $this->context->get(Context::KEY_FRAGMENT) ?: array();
    }

    public function getApplicationStack()
    {
        return array_merge(
            $this->getPreFilter(),
            array(new ControllerExecutor($this, $this->context)),
            $this->getPostFilter()
        );
    }

    public function getPreFilter()
    {
        return array();
    }

    public function getPostFilter()
    {
        return array();
    }

    public function onLoad()
    {
        // we change the supported writer only if available
        $supportedWriter = $this->getSupportedWriter();
        if (!empty($supportedWriter)) {
            $this->context->set(Context::KEY_SUPPORTED_WRITER, $supportedWriter);
        }
    }

    /**
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.1
     */
    public function onGet()
    {
    }

    /**
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.2
     */
    public function onHead()
    {
    }

    /**
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.3
     */
    public function onPost()
    {
    }

    /**
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.4
     */
    public function onPut()
    {
    }

    /**
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.5
     */
    public function onDelete()
    {
    }

    /**
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.7
     */
    public function onOptions()
    {
    }

    /**
     * @see https://tools.ietf.org/html/rfc5789#section-2
     */
    public function onPatch()
    {
    }

    public function processResponse()
    {
        $body = $this->response->getBody();

        if ($body->tell() == 0 && !$this->_responseWritten) {
            $this->setBody(new Record());
        }
    }

    /**
     * Returns an specific uri fragment
     *
     * @param string $key
     * @return string
     */
    protected function getUriFragment($key)
    {
        return isset($this->uriFragments[$key]) ? $this->uriFragments[$key] : null;
    }

    /**
     * Returns the result of the reader for the request
     *
     * @param string $readerType
     * @return mixed
     */
    protected function getBody($readerType = null)
    {
        $data    = (string) $this->request->getBody();
        $payload = Payload::create($data, $this->request->getHeader('Content-Type'))
            ->setRwType($readerType);

        return $this->io->parse($payload);
    }

    /**
     * @param string $schema
     * @param \PSX\Schema\Validation\ValidatorInterface $validator
     * @param string $readerType
     * @return mixed
     */
    protected function getBodyAs($schema, ValidatorInterface $validator = null, $readerType = null)
    {
        $data    = (string) $this->request->getBody();
        $payload = Payload::create($data, $this->request->getHeader('Content-Type'))
            ->setRwType($readerType);

        return $this->io->read($schema, $payload, new TypeVisitor($validator));
    }

    /**
     * Method to set a response body
     *
     * @param mixed $data
     * @param string $writerType
     */
    protected function setBody($data, $writerType = null)
    {
        if ($this->_responseWritten) {
            // we have already written a response
            return;
        }

        if ($data instanceof DOMDocument) {
            if (!$this->response->hasHeader('Content-Type')) {
                $this->response->setHeader('Content-Type', 'application/xml');
            }

            $this->response->getBody()->write($data->saveXML());
        } elseif ($data instanceof SimpleXMLElement) {
            if (!$this->response->hasHeader('Content-Type')) {
                $this->response->setHeader('Content-Type', 'application/xml');
            }

            $this->response->getBody()->write($data->asXML());
        } elseif ($data instanceof StreamInterface) {
            $this->response->setBody($data);
        } elseif (is_string($data)) {
            $this->response->getBody()->write($data);
        } else {
            $this->setResponse($data, $writerType);
        }

        $this->_responseWritten = true;
    }

    /**
     * Configures the writer
     *
     * @param \PSX\Data\WriterInterface $writer
     */
    protected function configureWriter(WriterInterface $writer)
    {
        if ($writer instanceof FrameworkWriter\TemplateAbstract) {
            if (!$writer->getControllerFile()) {
                $class = new ReflectionClass($this);
                $writer->setControllerFile($class->getFilename());
            }
        } elseif ($writer instanceof Writer\Soap) {
            if (!$writer->getRequestMethod()) {
                $writer->setRequestMethod($this->request->getMethod());
            }
        } elseif ($writer instanceof Writer\Jsonp) {
            if (!$writer->getCallbackName()) {
                $writer->setCallbackName($this->getParameter('callback'));
            }
        }
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
     * Writes the $record with the writer $writerType or depending on the get
     * parameter format or of the mime type of the Accept header
     *
     * @param mixed $data
     * @param string $writerType
     * @return void
     */
    private function setResponse($data, $writerType = null)
    {
        $contentType = $this->getHeader('Accept');
        $format      = $this->getParameter('format');

        if (!empty($format) && $writerType === null) {
            $writerType = $this->io->getConfiguration()->getWriterFactory()->getWriterClassNameByFormat($format);
        }

        $supported = $this->context->get(Context::KEY_SUPPORTED_WRITER);
        $writer    = $this->io->getWriter($contentType, $writerType, $supported);

        // set writer specific settings
        $this->configureWriter($writer);

        // write the response
        $payload = Payload::create($data, $contentType)
            ->setRwType($writerType);

        if (!empty($supported)) {
            $payload->setRwSupported($supported);
        }

        $response = $this->io->write($payload);

        // the response may have multiple presentations based on the Accept
        // header field
        if (!$this->response->hasHeader('Vary')) {
            $this->response->setHeader('Vary', 'Accept');
        }

        // set content type header if not available
        if (!$this->response->hasHeader('Content-Type')) {
            $contentType = $writer->getContentType();

            if ($contentType !== null) {
                $this->response->setHeader('Content-Type', $contentType);
            }
        }

        // for head requests set content length and remove body
        if ($this->request->getMethod() == 'HEAD') {
            $this->response->setHeader('Content-Length', mb_strlen($response));
            $response = '';
        }

        // for iframe file uploads we need a text/html content type header even
        // if we want serve json content. If all browsers support the FormData
        // api we can send file uploads per ajax but for now we use this hack.
        // Note do not rely on this param it will be removed as soon as possible
        if (isset($_GET['htmlMime'])) {
            $this->response->setHeader('Content-Type', 'text/html');
        }

        $this->response->getBody()->write($response);
    }
}
