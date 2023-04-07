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

namespace PSX\Framework\Tests\Loader;

use PHPUnit\Framework\TestCase;
use PSX\Framework\Loader\Context;
use PSX\Framework\Loader\ContextFactoryInterface;
use PSX\Framework\Test\Environment;

/**
 * ContextTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ContextTest extends TestCase
{
    public function testContext()
    {
        $context = Environment::getService(ContextFactoryInterface::class)->factory();

        $this->assertInstanceOf(Context::class, $context);

        $context->setPath('/foo');
        $context->setParameters(['foo' => 'bar']);
        $context->setSource([\stdClass::class, 'foo']);
        $context->setException(new \InvalidArgumentException('foo'));
        $context->setVersion('1');
        
        $this->assertEquals('/foo', $context->getPath());
        $this->assertEquals(['foo' => 'bar'], $context->getParameters());
        $this->assertEquals('bar', $context->getParameter('foo'));
        $this->assertEquals([\stdClass::class, 'foo'], $context->getSource());
        $this->assertInstanceOf(\InvalidArgumentException::class, $context->getException());
        $this->assertEquals('1', $context->getVersion());
    }
}
