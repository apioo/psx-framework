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

namespace PSX\Framework\Environment\WebServer;

use PSX\Http\Response;
use PSX\Http\Stream\TempStream;

/**
 * ResponseFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * @var array
     */
    protected $server;

    /**
     * @param array|null $server
     */
    public function __construct(array $server = null)
    {
        $this->server = $server === null ? $_SERVER : $server;
    }

    /**
     * @inheritdoc
     */
    public function createResponse()
    {
        $protocol = isset($this->server['SERVER_PROTOCOL']) ? $this->server['SERVER_PROTOCOL'] : 'HTTP/1.1';
        $response = new Response();
        $response->setProtocolVersion($protocol);
        $response->setHeader('X-Powered-By', 'psx');
        $response->setBody(new TempStream(fopen('php://temp', 'r+')));

        return $response;
    }
}
