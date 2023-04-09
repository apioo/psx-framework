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

namespace PSX\Framework\Tests\Config;

use PHPUnit\Framework\TestCase;
use PSX\Framework\Config\Config;
use PSX\Framework\Config\NotFoundException;

/**
 * ConfigTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ConfigTest extends TestCase
{
    public function testConstructorArray()
    {
        $config = new Config([
            'foo' => 'bar'
        ]);

        $this->assertEquals('bar', $config['foo']);
        $this->assertEquals('bar', $config->get('foo'));
    }

    public function testDefinitionConfig()
    {
        $this->expectException(NotFoundException::class);

        $config = Config::fromFile(__DIR__ . '/definition_config.php');

        $this->assertEquals('bar', $config['foo']);
        $this->assertEquals('bar', $config->get('foo'));
    }

    public function testDefinitionConfigInvalidType()
    {
        $this->expectException(NotFoundException::class);

        Config::fromFile(__DIR__ . '/definition_invalid_config_type.php');
    }

    public function testReturnConfig()
    {
        $config = Config::fromFile(__DIR__ . '/return_config.php');

        $this->assertEquals('bar', $config['foo']);
        $this->assertEquals('bar', $config->get('foo'));
    }

    public function testReturnConfigInvalidType()
    {
        $this->expectException(NotFoundException::class);

        Config::fromFile(__DIR__ . '/return_invalid_config_type.php');
    }

    public function testNoConfig()
    {
        $this->expectException(NotFoundException::class);

        Config::fromFile(__DIR__ . '/no_config.php');
    }

    public function testConfigFileNotExisting()
    {
        $this->expectException(\ErrorException::class);

        Config::fromFile(__DIR__ . '/foo_config.php');
    }

    public function testConfigMerge()
    {
        $config = new Config(['foo' => 'bar']);
        $config['foo'] = 'bar';

        $configA = new Config(['bar' => 'foo']);
        $configA->putAll($config);

        $this->assertEquals('foo', $configA->get('bar'));
        $this->assertEquals('bar', $configA->get('foo'));
    }
}
