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

namespace PSX\Framework\Config;

use PSX\Data\WriterInterface;

/**
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ConfigFactory
{
    private static ?Config $config = null;

    /**
     * @throws NotFoundException
     */
    public static function factory(string $appDir): Config
    {
        if (isset(self::$config)) {
            return self::$config;
        }

        $config = new Config(self::getDefaultConfig());
        $config->putAll(Config::fromFile($appDir . '/configuration.php'));
        return self::$config = $config;
    }

    private static function getDefaultConfig(): array
    {
        return [
            'psx_env'                 => 'prod',
            'psx_debug'               => false,
            'psx_dispatch'            => 'index.php/',
            'psx_connection'          => 'pdo-sqlite://:memory:',
            'psx_mailer'              => 'native://default',
            'psx_messenger'           => 'in-memory://',
            'psx_migration_namespace' => 'App\\Migrations',
            'psx_cors_origin'         => '*',
            'psx_cors_headers'        => ['Accept', 'Accept-Language', 'Authorization', 'Content-Language', 'Content-Type', 'User-Agent'],
            'psx_supported_writer'    => [
                WriterInterface::FORM,
                WriterInterface::JSON,
                WriterInterface::JSONP,
                WriterInterface::JSONX,
                WriterInterface::SOAP,
                WriterInterface::XML,
            ],
        ];
    }
}
