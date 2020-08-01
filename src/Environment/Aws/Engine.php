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

namespace PSX\Framework\Environment\Aws;

use GuzzleHttp\Client;
use PSX\Framework\Config\Config;
use PSX\Framework\Dispatch\Dispatch;
use PSX\Framework\Environment\EngineInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Http\Server\RequestFactory;
use PSX\Http\Server\ResponseFactory;
use PSX\Http\Stream\Stream;
use PSX\Http\Stream\StringStream;
use PSX\Uri\Uri;

/**
 * Interacts with the AWS HTTP API
 *
 * @see     https://github.com/spiral/roadrunner
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Engine implements EngineInterface
{
    /**
     * @var Client
     */
    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
     * @inheritdoc
     */
    public function serve(Dispatch $dispatch, Config $config)
    {
        while (true) {
            $request  = (new RequestFactory())->createRequest();
            $response = (new ResponseFactory())->createResponse();

            $invocationId = $this->getNextRequest($request);

            $dispatch->route($request, $response);

            $this->sendResponse($response, $invocationId);
        }
    }
    
    private function getNextRequest(RequestInterface $request)
    {
        $response = $this->httpClient->get('http://' . $_ENV['AWS_LAMBDA_RUNTIME_API'] . '/2018-06-01/runtime/invocation/next');
        $invocationId = $response->getHeader('Lambda-Runtime-Aws-Request-Id')[0];
        $data = \json_decode((string) $response->getBody(), true);

        $method = $data['requestContext']['http']['method'];
        $uri = $data['rawPath'];

        if (!empty($data['rawQueryString'])) {
            $uri.= '?' . http_build_query($data['rawQueryString'], '', '&');
        }

        if ($data['isBase64Encoded'] ?? false) {
            $handle = fopen('php://temp', 'r+');
            fwrite($handle, base64_decode($data['body']));
            fseek($handle, 0);

            $body = new Stream($handle);
        } else {
            $body = new StringStream($data['body']);
        }

        $request->setMethod($method);
        $request->setUri(new Uri($uri));
        $request->setHeaders($data['headers']);
        $request->setBody($body);

        return $invocationId;
    }
    
    private function sendResponse(ResponseInterface $response, $invocationId)
    {
        $body = (string) $response->getBody();

        $headers = [];
        foreach ($response->getHeaders() as $name => $header) {
            $headers[$name] = implode(', ', $header);
        }

        $data = [
            'statusCode' => $response->getStatusCode() ?: 200,
            'headers' => $headers,
            'body' => $body,
        ];

        $this->httpClient->post(
            'http://' . $_ENV['AWS_LAMBDA_RUNTIME_API'] . '/2018-06-01/runtime/invocation/' . $invocationId . '/response',
            ['body' => \json_encode($data)]
        );
    }
}
