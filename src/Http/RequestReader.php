<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Http\RequestInterface;
use PSX\Schema\Validation\ValidatorInterface;
use PSX\Schema\Visitor\TypeVisitor;

/**
 * RequestReader
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class RequestReader
{
    private Processor $processor;

    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Returns the result of the reader for the request
     */
    public function getBody(RequestInterface $request, ?string $readerType = null): mixed
    {
        $data = (string) $request->getBody();

        $payload = Payload::create($data, $request->getHeader('Content-Type'));
        if ($readerType !== null) {
            $payload->setRwType($readerType);
        }

        return $this->processor->parse($payload);
    }

    public function getBodyAs(RequestInterface $request, mixed $schema, ?ValidatorInterface $validator = null, ?string $readerType = null): mixed
    {
        $data = (string) $request->getBody();

        $payload = Payload::create($data, $request->getHeader('Content-Type'));
        if ($readerType !== null) {
            $payload->setRwType($readerType);
        }

        return $this->processor->read($schema, $payload, new TypeVisitor($validator));
    }
}
