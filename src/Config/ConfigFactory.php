<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Data\WriterInterface;
use PSX\Framework\Loader\Context;
use PSX\Framework\Logger\LoggerFactory;

/**
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ConfigFactory
{
    /**
     * @throws NotFoundException
     */
    public static function factory(string $configFile): Config
    {
        $config = new Config(self::getDefaultConfig());
        return $config->merge(Config::fromFile($configFile));
    }

    private static function getDefaultConfig(): array
    {
        return [
            'psx_dispatch'            => 'index.php/',
            'psx_connection'          => [
                'memory'              => true,
                'driver'              => 'pdo_sqlite',
            ],
            'psx_migration_namespace' => 'App\\Migrations',
            'psx_cors_origin'         => '*',
            'psx_cors_headers'        => ['Accept', 'Accept-Language', 'Authorization', 'Content-Language', 'Content-Type'],
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
