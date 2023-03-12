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

namespace PSX\Framework\Config;

use ArrayIterator;
use PSX\Data\WriterInterface;
use PSX\Framework\Connection\CacheFactory;
use PSX\Framework\Loader\Context;
use PSX\Framework\Loader\ContextFactory;
use PSX\Framework\Log\LoggerFactory;

/**
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ConfigFactory
{
    /**
     * @throws NotFoundException
     */
    public function factory(string $configFile): Config
    {
        $config = new Config($this->getDefaultConfig());
        return $config->merge(Config::fromFile($configFile));
    }

    private function getDefaultConfig(): array
    {
        return [
            'psx_dispatch'            => 'index.php/',
            'psx_connection'          => [
                'memory'              => true,
                'driver'              => 'pdo_sqlite',
            ],
            'psx_cors_origin'         => '*',
            'psx_cors_headers'        => ['Accept', 'Accept-Language', 'Authorization', 'Content-Language', 'Content-Type'],
            'psx_context_class'       => Context::class,
            'psx_logger_factory'      => LoggerFactory::class,
            'psx_supported_writer'    => [
                WriterInterface::ATOM,
                WriterInterface::FORM,
                WriterInterface::JSON,
                WriterInterface::JSONP,
                WriterInterface::JSONX,
                WriterInterface::RSS,
                WriterInterface::SOAP,
                WriterInterface::XML,
            ],
        ];
    }
}
