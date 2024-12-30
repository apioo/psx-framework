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

/**
 * WriterOptions
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class WriterOptions
{
    private ?string $writerType = null;
    private ?string $contentType = null;
    private ?string $format = null;
    private ?array $supportedWriter = null;
    private ?\Closure $writerCallback = null;

    public function getWriterType(): ?string
    {
        return $this->writerType;
    }

    public function setWriterType(string $writerType): void
    {
        $this->writerType = $writerType;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function getSupportedWriter(): ?array
    {
        return $this->supportedWriter;
    }

    public function setSupportedWriter(array $supportedWriter): void
    {
        $this->supportedWriter = $supportedWriter;
    }

    public function getWriterCallback(): ?\Closure
    {
        return $this->writerCallback;
    }

    public function setWriterCallback(\Closure $writerCallback): void
    {
        $this->writerCallback = $writerCallback;
    }
}
