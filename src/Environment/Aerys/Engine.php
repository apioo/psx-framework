<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use Amp\ByteStream\ResourceOutputStream;
use Amp\Http\Server\Request as AerysRequest;
use Amp\Http\Server\RequestHandler\CallableRequestHandler;
use Amp\Http\Server\Response as AerysResponse;
use Amp\Http\Server\Server;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Loop;
use Amp\Socket;
use Monolog\Logger;
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
 * @see     https://github.com/amphp/http-server
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
    public function __construct($ip = '0.0.0.0', $port = 8080)
    {
        $this->ip   = $ip;
        $this->port = $port;
    }

    /**
     * @inheritdoc
     */
    public function serve(Dispatch $dispatch, Config $config)
    {
        Loop::run(function () use ($dispatch) {
            $servers = [
                Socket\listen("{$this->ip}:{$this->port}"),
            ];

            // logger
            $logHandler = new StreamHandler(new ResourceOutputStream(\STDOUT));
            $logHandler->setFormatter(new ConsoleFormatter());
            $logger = new Logger('server');
            $logger->pushHandler($logHandler);

            // server
            $callable = function (AerysRequest $aerysRequest) use ($dispatch) {
                $request  = new Request(new Uri($aerysRequest->getUri()->__toString()), $aerysRequest->getMethod(), $aerysRequest->getHeaders());
                $response = (new ResponseFactory())->createResponse();

                // read body
                if (in_array($aerysRequest->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
                    $body = yield $aerysRequest->getBody()->buffer();
                    $request->setBody(new StringStream($body));
                }

                $response = $dispatch->route($request, $response);

                // send response
                return new AerysResponse($response->getStatusCode() ?: 200, $response->getHeaders(), $response->getBody()->__toString());
            };

            $server = new Server($servers, new CallableRequestHandler($callable), $logger);

            yield $server->start();
        });
    }
}
