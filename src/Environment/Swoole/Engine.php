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

namespace PSX\Framework\Environment\Swoole;

use PSX\Framework\Config\Config;
use PSX\Framework\Dispatch\Dispatch;
use PSX\Framework\Environment\EngineInterface;
use PSX\Framework\Environment\WebServer\ResponseFactory;
use PSX\Http\Request;
use PSX\Http\Stream\StringStream;
use PSX\Uri\Uri;

/**
 * Uses the Swoole HTTP server
 *
 * @see     https://github.com/swoole/swoole-src
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
     * @var array
     */
    protected $options;

    /**
     * @param string $ip
     * @param integer $port
     * @param array|null $options
     */
    public function __construct($ip = '0.0.0.0', $port = 8080, array $options = null)
    {
        $this->ip      = $ip;
        $this->port    = $port;
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function serve(Dispatch $dispatch, Config $config)
    {
        $server = new \swoole_http_server($this->ip, $this->port);

        if (!empty($this->options)) {
            $server->set($this->options);
        }

        $server->on('request', function (\swoole_http_request $swooleRequest, \swoole_http_response $swooleResponse) use ($dispatch) {
            $this->process($swooleRequest, $swooleResponse, $dispatch);
        });

        $server->start();
    }

    private function process(\swoole_http_request $swooleRequest, \swoole_http_response $swooleResponse, Dispatch $dispatch)
    {
        $uri = new Uri($swooleRequest->server['request_uri']);
        if ($swooleRequest->get) {
            $uri = $uri->withParameters($swooleRequest->get);
        }

        $request  = new Request($uri, $swooleRequest->server['request_method'], $swooleRequest->header);
        $response = (new ResponseFactory())->createResponse();

        // read body
        if (in_array($swooleRequest->server['request_method'], ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $request->setBody(new StringStream($swooleRequest->rawContent()));
        }

        $response = $dispatch->route($request, $response);

        // send response
        $swooleResponse->status($response->getStatusCode() ?: 200);

        $headers = $response->getHeaders();
        foreach ($headers as $name => $value) {
            $swooleResponse->header($name, implode(', ', $value));
        }

        $swooleResponse->end($response->getBody()->__toString());
    }
}
