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

/**
 * WriterOptions
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class WriterOptions
{
    /**
     * @var string
     */
    protected $writerType;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var array
     */
    protected $supportedWriter;

    /**
     * @var \Closure
     */
    protected $writerCallback;

    /**
     * @return string
     */
    public function getWriterType()
    {
        return $this->writerType;
    }

    /**
     * @param string $writerType
     */
    public function setWriterType($writerType)
    {
        $this->writerType = $writerType;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }
    
    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return array
     */
    public function getSupportedWriter()
    {
        return $this->supportedWriter;
    }

    /**
     * @param array $supportedWriter
     */
    public function setSupportedWriter(array $supportedWriter)
    {
        $this->supportedWriter = $supportedWriter;
    }

    /**
     * @return \Closure
     */
    public function getWriterCallback()
    {
        return $this->writerCallback;
    }

    /**
     * @param \Closure $writerCallback
     */
    public function setWriterCallback(\Closure $writerCallback)
    {
        $this->writerCallback = $writerCallback;
    }
}
