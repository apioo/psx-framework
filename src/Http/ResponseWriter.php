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

namespace PSX\Framework\Http;

use PSX\Data\Payload;
use PSX\Data\Processor;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\StringStream;
use PSX\Http\StreamInterface;

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
     * @param \PSX\Data\Processor $processor
     */
    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Method to set a response body
     *
     * @param \PSX\Http\ResponseInterface $response
     * @param mixed $data
     * @param \PSX\Framework\Http\WriterOptions $options
     */
    public function setBody(ResponseInterface $response, $data, WriterOptions $options = null)
    {
        if ($data instanceof \DOMDocument) {
            $data = new Body\Xml($data);
        } elseif ($data instanceof \SimpleXMLElement) {
            $data = new Body\Xml($data);
        } elseif ($data instanceof StreamInterface) {
            $data = new Body\Stream($data);
        } elseif (is_string($data)) {
            $data = new Body\Body($data);
        }

        // set new response body since we want to discard every data which was
        // written before because this could corrupt our output format
        $response->setBody(new StringStream());

        if ($data instanceof Body\BodyInterface) {
            $data->writeTo($response);
        } else {
            $this->setResponse($response, $data, $options ?? new WriterOptions());
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
        // header field
        if (!$response->hasHeader('Vary')) {
            $response->setHeader('Vary', 'Accept');
        }

        // set content type header if not available
        if (!$response->hasHeader('Content-Type')) {
            $contentType = $writer->getContentType();

            if ($contentType !== null) {
                $response->setHeader('Content-Type', $contentType);
            }
        }

        // for head requests set content length and remove body
        if ($options->getRequestMethod() == 'HEAD') {
            $response->setHeader('Content-Length', mb_strlen($result));
            $result = '';
        }

        $response->getBody()->write($result);
    }
}
