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

namespace PSX\Framework\Mailer;

use Psr\EventDispatcher\EventDispatcherInterface;
use PSX\Framework\Config\ConfigInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;

/**
 * MailerFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class MailerFactory
{
    private string $dsn;
    private ConfigInterface $config;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(string $dsn, ConfigInterface $config, EventDispatcherInterface $eventDispatcher)
    {
        $this->dsn = $dsn;
        $this->config = $config;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function factory(): MailerInterface
    {
        if ($this->config->get('psx_debug') === true) {
            return new Mailer(new Transport\NullTransport(), null, $this->eventDispatcher);
        } else {
            return new Mailer(Transport::fromDsn($this->dsn), null, $this->eventDispatcher);
        }
    }
}
