<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Environment\CLI;

use PSX\Engine\DispatchInterface;
use PSX\Engine\EngineInterface;
use PSX\Http\Request;
use PSX\Http\Server\ResponseFactory;
use PSX\Uri\Uri;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Reads and writes HTTP requests to and from symfony console interfaces
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Engine implements EngineInterface
{
    private InputInterface $input;
    private OutputInterface $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;
    }

    /**
     * @inheritdoc
     */
    public function serve(DispatchInterface $dispatch): void
    {
        $request  = $this->createRequest($this->input->getArgument('method'), $this->input->getArgument('uri'), $this->input->getArgument('headers'));
        $response = (new ResponseFactory())->createResponse();

        $response = $dispatch->route($request, $response);

        $this->output->write($response->getBody()->__toString());
    }

    private function createRequest(string $method, string $uri, ?string $rawHeaders = null)
    {
        $headers = [];
        if (!empty($rawHeaders)) {
            parse_str($rawHeaders, $headers);
        }

        $body = null;
        if ($this->input instanceof StreamableInputInterface && in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $body = $this->readStdin($this->input->getStream());
        }

        return new Request(
            new Uri($uri),
            $method,
            $headers,
            $body
        );
    }

    private function readStdin($handle): string
    {
        $body = '';

        while (!feof($handle)) {
            $line = fgets($handle);
            $pos  = strpos($line, chr(4));

            if ($pos !== false) {
                $body.= substr($line, 0, $pos);
                break;
            }

            $body.= $line;
        }

        return $body;
    }
}
