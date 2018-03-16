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

namespace PSX\Framework\Tests\Config;

use PSX\Framework\Config\Config;

/**
 * ConfigTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorArray()
    {
        $config = new Config([
            'foo' => 'bar'
        ]);

        $this->assertEquals('bar', $config['foo']);
        $this->assertEquals('bar', $config->get('foo'));
    }

    /**
     * @expectedException \PSX\Framework\Config\NotFoundException
     */
    public function testDefinitionConfig()
    {
        $config = Config::fromFile(__DIR__ . '/definition_config.php');

        $this->assertEquals('bar', $config['foo']);
        $this->assertEquals('bar', $config->get('foo'));
    }

    /**
     * @expectedException \PSX\Framework\Config\NotFoundException
     */
    public function testDefinitionConfigInvalidType()
    {
        Config::fromFile(__DIR__ . '/definition_invalid_config_type.php');
    }

    public function testReturnConfig()
    {
        $config = Config::fromFile(__DIR__ . '/return_config.php');

        $this->assertEquals('bar', $config['foo']);
        $this->assertEquals('bar', $config->get('foo'));
    }

    /**
     * @expectedException \PSX\Framework\Config\NotFoundException
     */
    public function testReturnConfigInvalidType()
    {
        Config::fromFile(__DIR__ . '/return_invalid_config_type.php');
    }

    /**
     * @expectedException \PSX\Framework\Config\NotFoundException
     */
    public function testNoConfig()
    {
        Config::fromFile(__DIR__ . '/no_config.php');
    }

    /**
     * @expectedException \ErrorException
     */
    public function testConfigFileNotExisting()
    {
        Config::fromFile(__DIR__ . '/foo_config.php');
    }

    public function testConfigOffsetSet()
    {
        $config = new Config([]);
        $config['foo'] = 'bar';

        $this->assertEquals('bar', $config['foo']);

        $config->set('bar', 'foo');

        $this->assertEquals('foo', $config['bar']);
    }

    public function testConfigOffsetExists()
    {
        $config = new Config([]);

        $this->assertEquals(false, isset($config['foobar']));
        $this->assertEquals(false, $config->has('foobar'));

        $config['foobar'] = 'test';

        $this->assertEquals(true, isset($config['foobar']));
        $this->assertEquals(true, $config->has('foobar'));
    }

    public function testConfigOffsetUnset()
    {
        $config = new Config([]);
        $config['bar'] = 'test';

        unset($config['bar']);

        $this->assertEquals(true, !isset($config['bar']));
    }

    public function testConfigOffsetGet()
    {
        $config = new Config([]);
        $config['bar'] = 'test';

        $this->assertEquals('test', $config['bar']);
        $this->assertEquals('test', $config->get('bar'));
    }

    public function testConfigOffsetGetNotExisting()
    {
        $config = new Config([]);

        $this->assertNull($config['bar']);
        $this->assertNull($config->get('bar'));
    }

    public function testConfigMerge()
    {
        $config = new Config(['foo' => 'bar']);
        $config['foo'] = 'bar';

        $configA = new Config(['bar' => 'foo']);
        $configB = $configA->merge($config);

        $this->assertEquals('foo', $configB->get('bar'));
        $this->assertEquals('bar', $configB->get('foo'));
    }
}
