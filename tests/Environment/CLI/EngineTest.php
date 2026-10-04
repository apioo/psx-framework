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

namespace PSX\Framework\Tests\Environment\CLI;

use PHPUnit\Framework\TestCase;
use PSX\Framework\Environment\CLI\Engine;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * EngineTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class EngineTest extends TestCase
{
    public function testServeDefaultUserAgent(): void
    {
        $dispatch = new TestDispatch(200);

        $engine = new Engine($this->newInput('GET', '/foo'), new BufferedOutput());
        $engine->serve($dispatch);

        $this->assertSame(Engine::USER_AGENT, $dispatch->request?->getHeader('User-Agent'));
    }

    public function testServeCustomUserAgent(): void
    {
        $dispatch = new TestDispatch(200);

        $engine = new Engine($this->newInput('GET', '/foo', 'user-agent=my-client'), new BufferedOutput());
        $engine->serve($dispatch);

        $this->assertSame('my-client', $dispatch->request?->getHeader('User-Agent'));
    }

    public function testServeStatusLine(): void
    {
        $dispatch = new TestDispatch(404);
        $output = new TestConsoleOutput();

        $engine = new Engine($this->newInput('GET', '/foo'), $output);
        $engine->serve($dispatch);

        $this->assertSame(404, $engine->getStatusCode());
        $this->assertSame('body', $output->fetch());
        $this->assertSame('HTTP/1.1 404 Not Found' . PHP_EOL, $output->getErrorOutput()->fetch());
    }

    private function newInput(string $method, string $uri, ?string $headers = null): ArrayInput
    {
        $definition = new InputDefinition([
            new InputArgument('method', InputArgument::REQUIRED),
            new InputArgument('uri', InputArgument::REQUIRED),
            new InputArgument('headers', InputArgument::OPTIONAL),
        ]);

        return new ArrayInput(['method' => $method, 'uri' => $uri, 'headers' => $headers], $definition);
    }
}
