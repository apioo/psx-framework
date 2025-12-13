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

namespace PSX\Framework\Logger;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * LoggerFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class LoggerFactory
{
    public function __construct(private string $logDir, private int|Level $logLevel)
    {
    }

    public function factory(string $namespace = 'psx'): LoggerInterface
    {
        $logger = new Logger($namespace);
        $logger->pushHandler($this->newHandler());

        return $logger;
    }

    protected function newHandler(): HandlerInterface
    {
        $level = is_int($this->logLevel) ? Level::from($this->logLevel) : $this->logLevel;

        if ($this->logDir === 'php://error_log') {
            return new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, $level);
        } else {
            return new StreamHandler($this->logDir . '/app.log', $level);
        }
    }
}
