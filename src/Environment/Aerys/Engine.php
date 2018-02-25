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

namespace PSX\Framework\Environment\Aerys;

use Aerys\Request as AerysRequest;
use Aerys\Response as AerysResponse;
use PSX\Framework\Config\Config;
use PSX\Framework\Dispatch\Dispatch;
use PSX\Framework\Environment\EngineInterface;
use PSX\Framework\Environment\WebServer\ResponseFactory;
use PSX\Http\Request;
use PSX\Http\Stream\StringStream;
use PSX\Uri\Uri;

/**
 * Uses the Aerys HTTP server
 *
 * @see     https://github.com/amphp/aerys/
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Engine implements EngineInterface
{
    /**
     * @var string
     */
    protected $ip;

    /**
     * @var integer
     */
    protected $port;

    /**
     * @param string $ip
     * @param integer $port
     */
    public function __construct($ip = '*', $port = 8080)
    {
        $this->ip   = $ip;
        $this->port = $port;
    }

    /**
     * @inheritdoc
     */
    public function serve(Dispatch $dispatch, Config $config)
    {
        return (new \Aerys\Host())
            ->expose($this->ip, $this->port)
            ->use(function(AerysRequest $aerysRequest, AerysResponse $aerysResponse) use ($dispatch){
                $request  = new Request(new Uri($aerysRequest->getUri()), $aerysRequest->getMethod(), $aerysRequest->getAllHeaders());
                $response = (new ResponseFactory())->createResponse();

                // read body
                if (in_array($aerysRequest->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
                    $body = yield $aerysRequest->getBody();
                    $request->setBody(new StringStream($body));
                }

                $response = $dispatch->route($request, $response);

                // send response
                $aerysResponse->setStatus($response->getStatusCode() ?: 200);

                $headers = $response->getHeaders();
                foreach ($headers as $name => $value) {
                    foreach ($value as $val) {
                        $aerysResponse->addHeader($name, $val);
                    }
                }

                $aerysResponse->end($response->getBody()->__toString());
            });
    }
}
