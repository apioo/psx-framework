<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Data\Body;
use PSX\Data\Exception\WriteException;
use PSX\Data\Exception\WriterNotFoundException;
use PSX\Data\GraphTraverser;
use PSX\Data\Multipart\File;
use PSX\Data\Payload;
use PSX\Data\Processor;
use PSX\Data\Writer;
use PSX\Data\WriterInterface;
use PSX\Http\Environment\HttpResponseInterface;
use PSX\Http\Exception\InternalServerErrorException;
use PSX\Http\Exception\NotAcceptableException;
use PSX\Http\RequestInterface;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\LazyStream;
use PSX\Http\Stream\StringStream;
use PSX\Http\StreamInterface;
use PSX\Http\Writer as HttpWriter;

/**
 * ResponseWriter
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ResponseWriter
{
    private Processor $processor;
    private array $supportedWriter;

    public function __construct(Processor $processor, array $supportedWriter)
    {
        $this->processor = $processor;
        $this->supportedWriter = $supportedWriter;
    }

    /**
     * Uses the internal response writer to serialize arbitrary PHP data to string representation. If writer type is an
     * HTTP request object the write will look at the provided header to send the fitting data format which the client
     * has requested. If writer type is a writer class name the specified writer will be used. Otherwise we use JSON as
     * default data format
     */
    public function setBody(ResponseInterface $response, mixed $data, mixed $writerType = null)
    {
        if ($data instanceof HttpResponseInterface) {
            $statusCode = $data->getStatusCode();
            if (!empty($statusCode)) {
                $response->setStatus($statusCode);
            }

            $headers = $data->getHeaders();
            if (!empty($headers)) {
                foreach ($headers as $name => $header) {
                    $response->addHeader($name, $header);
                }
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
            } elseif ($body instanceof \DOMDocument || $body instanceof \SimpleXMLElement) {
                $writer = new HttpWriter\Xml($body);
            } elseif ($body instanceof StreamInterface) {
                $writer = new HttpWriter\Stream($body);
            } elseif (is_string($body)) {
                $writer = new HttpWriter\Writer($body);
            } elseif ($body instanceof Body\Json) {
                $writer = new HttpWriter\Json($body);
            } elseif ($body instanceof Body\Form) {
                $writer = new HttpWriter\Form($body->getAll());
            } elseif ($body instanceof Body\Multipart) {
                $writer = $this->buildMultipartWriter($body);
            }

            // set new response body since we want to discard every data which
            // was written before because this could corrupt our output format
            $response->setBody(new StringStream());

            if ($writer instanceof HttpWriter\WriterInterface) {
                $writer->writeTo($response);
            } else {
                $this->setResponse($response, $body, $options);
            }
        } else {
            $response->setStatus(204);
            $response->setBody(new StringStream(''));
        }
    }

    /**
     * Writes the $record with the writer $writerType or depending on the get parameter format or of the mime type of
     * the Accept header
     */
    private function setResponse(ResponseInterface $response, mixed $data, WriterOptions $options): void
    {
        $format     = $options->getFormat();
        $writerType = $options->getWriterType();

        if (!empty($format) && $writerType === null) {
            $writerType = $this->processor->getConfiguration()->getWriterFactory()->getWriterClassNameByFormat($format);
        }

        $supported = $options->getSupportedWriter();

        try {
            $writer = $this->processor->getWriter($options->getContentType(), $writerType, $supported);
        } catch (WriterNotFoundException $e) {
            throw new NotAcceptableException($e->getMessage(), previous: $e);
        }

        // set writer specific settings
        $callback = $options->getWriterCallback();
        if ($callback instanceof \Closure) {
            $callback($writer);
        }

        // write the response
        $payload = Payload::create($data, $options->getContentType());
        if ($writerType !== null) {
            $payload->setRwType($writerType);
        }

        if (!empty($supported)) {
            $payload->setRwSupported($supported);
        }

        try {
            $result = $this->processor->write($payload);
        } catch (WriteException $e) {
            throw new InternalServerErrorException($e->getMessage(), previous: $e);
        } catch (WriterNotFoundException $e) {
            throw new NotAcceptableException($e->getMessage(), previous: $e);
        }

        // the response may have multiple presentations based on the Accept
        // header field but only in case we have no fix writer type
        if ($writerType === null) {
            $response->addHeader('Vary', 'Accept');
        }

        // set content type header if not available
        if (!$response->hasHeader('Content-Type')) {
            $response->setHeader('Content-Type', $writer->getContentType());
        }

        $response->getBody()->write($result);
    }

    /**
     * Returns the writer options for the provided request and the current context of the controller
     */
    protected function getWriterOptions(RequestInterface $request): WriterOptions
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

    private function buildMultipartWriter(Body\Multipart $body): HttpWriter\Multipart
    {
        $writer = new HttpWriter\Multipart('form-data');

        foreach ($body->getAll() as $name => $value) {
            if ($value instanceof File) {
                $file = $value->getTmpName();
                if (!is_file($file)) {
                    throw new \RuntimeException('Provided multipart file "' . $file . '" does not exist');
                }

                $headers = [
                    'Content-Type' => $value->getType() ?: 'application/octet-stream',
                    'Content-Disposition' => 'form-data; name="' . $name . '"; filename="' . $value->getName() . '"',
                ];

                $writer->addPart(new Response(200, $headers, new LazyStream($file, 'rb')));
            } else {
                $headers = [
                    'Content-Disposition' => 'form-data; name="' . $name . '"',
                ];

                $writer->addPart(new Response(200, $headers, new StringStream((string) $value)));
            }
        }

        return $writer;
    }
}
