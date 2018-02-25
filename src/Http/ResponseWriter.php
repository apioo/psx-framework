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

namespace PSX\Framework\Http;

use PSX\Data\GraphTraverser;
use PSX\Data\Payload;
use PSX\Data\Writer;
use PSX\Data\Processor;
use PSX\Data\WriterInterface;
use PSX\Http\Environment\HttpResponseInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\StringStream;
use PSX\Http\StreamInterface;
use PSX\Http\Writer as HttpWriter;

/**
 * ResponseWriter
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResponseWriter
{
    /**
     * @var \PSX\Data\Processor
     */
    protected $processor;

    /**
     * @var array
     */
    protected $supportedWriter;

    /**
     * @param \PSX\Data\Processor $processor
     * @param array $supportedWriter
     */
    public function __construct(Processor $processor, array $supportedWriter = [])
    {
        $this->processor       = $processor;
        $this->supportedWriter = $supportedWriter;
    }

    /**
     * Uses the internal response writer to serialize arbitrary PHP data to
     * string representation. If writer type is a HTTP request object the write
     * will look at the provided header to send the fitting data format which
     * the client has requested. If writer type is a writer class name the
     * specified writer will be used. Otherwise we use JSON as default data
     * format
     *
     * @param \PSX\Http\ResponseInterface $response
     * @param mixed $data
     * @param \PSX\Framework\Http\WriterOptions|\PSX\Http\RequestInterface|string|null $writerType
     */
    public function setBody(ResponseInterface $response, $data, $writerType = null)
    {
        if ($data instanceof HttpResponseInterface) {
            $statusCode = $data->getStatusCode();
            if (!empty($statusCode)) {
                $response->setStatus($statusCode);
            }

            $headers = $data->getHeaders();
            if (!empty($headers)) {
                $response->setHeaders($headers);
            }

            $body = $data->getBody();
        } else {
            $body = $data;
        }

        if (!GraphTraverser::isEmpty($body)) {
            if ($writerType instanceof WriterOptions) {
                $options = $writerType;
            } elseif ($writerType instanceof RequestInterface) {
                $options = $this->getWriterOptions($writerType);
            } elseif (is_string($writerType)) {
                $options = new WriterOptions();
                $options->setWriterType($writerType);
            } else {
                $options = new WriterOptions();
                $options->setWriterType(WriterInterface::JSON);
            }

            $writer = null;
            if ($body instanceof HttpWriter\WriterInterface) {
                $writer = $body;
            } elseif ($body instanceof \DOMDocument) {
                $writer = new HttpWriter\Xml($body);
            } elseif ($body instanceof \SimpleXMLElement) {
                $writer = new HttpWriter\Xml($body);
            } elseif ($body instanceof StreamInterface) {
                $writer = new HttpWriter\Stream($body);
            } elseif (is_string($body)) {
                $writer = new HttpWriter\Writer($body);
            }

            // set new response body since we want to discard every data which
            // was written before because this could corrupt our output format
            $response->setBody(new StringStream());

            if ($writer instanceof HttpWriter\WriterInterface) {
                $writer->writeTo($response);
            } else {
                $this->setResponse($response, $body, $options ?? new WriterOptions());
            }
        } else {
            $response->setStatus(204);
            $response->setBody(new StringStream(''));
        }
    }

    /**
     * Writes the $record with the writer $writerType or depending on the get
     * parameter format or of the mime type of the Accept header
     *
     * @param \PSX\Http\ResponseInterface $response
     * @param mixed $data
     * @param \PSX\Framework\Http\WriterOptions $options
     * @return void
     */
    private function setResponse(ResponseInterface $response, $data, WriterOptions $options)
    {
        $format     = $options->getFormat();
        $writerType = $options->getWriterType();

        if (!empty($format) && $writerType === null) {
            $writerType = $this->processor->getConfiguration()->getWriterFactory()->getWriterClassNameByFormat($format);
        }

        $supported = $options->getSupportedWriter();
        $writer    = $this->processor->getWriter($options->getContentType(), $writerType, $supported);

        // set writer specific settings
        $callback = $options->getWriterCallback();
        if ($callback instanceof \Closure) {
            $callback($writer);
        }

        // write the response
        $payload = Payload::create($data, $options->getContentType())
            ->setRwType($writerType);

        if (!empty($supported)) {
            $payload->setRwSupported($supported);
        }

        $result = $this->processor->write($payload);

        // the response may have multiple presentations based on the Accept
        // header field but only in case we have no fix writer type
        if ($writerType === null && !$response->hasHeader('Vary')) {
            $response->setHeader('Vary', 'Accept');
        }

        // set content type header if not available
        if (!$response->hasHeader('Content-Type')) {
            $contentType = $writer->getContentType();

            if ($contentType !== null) {
                $response->setHeader('Content-Type', $contentType);
            }
        }

        $response->getBody()->write($result);
    }

    /**
     * Returns the writer options for the provided request and the current
     * context of the controller
     *
     * @param \PSX\Http\RequestInterface $request
     * @return \PSX\Framework\Http\WriterOptions
     */
    protected function getWriterOptions(RequestInterface $request)
    {
        $options = new WriterOptions();
        $options->setContentType($request->getHeader('Accept'));
        $options->setFormat($request->getUri()->getParameter('format'));
        $options->setSupportedWriter($this->supportedWriter);
        $options->setWriterCallback(function(WriterInterface $writer) use ($request){
            if ($writer instanceof Writer\Jsonp) {
                if (!$writer->getCallbackName()) {
                    $writer->setCallbackName($request->getUri()->getParameter('callback'));
                }
            }
        });

        return $options;
    }
}
