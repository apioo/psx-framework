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

namespace PSX\Framework\Tests\Loader;

use PSX\Framework\Loader\Context;

/**
 * ContextTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    public function testContext()
    {
        $context = new Context();

        $context->setPath('/foo');
        $context->setParameters(['foo' => 'bar']);
        $context->setSource('foo');
        $context->setException(new \InvalidArgumentException('foo'));
        $context->setVersion('1');
        
        $this->assertEquals('/foo', $context->getPath());
        $this->assertEquals(['foo' => 'bar'], $context->getParameters());
        $this->assertEquals('bar', $context->getParameter('foo'));
        $this->assertEquals('foo', $context->getSource());
        $this->assertInstanceOf(\InvalidArgumentException::class, $context->getException());
        $this->assertEquals('1', $context->getVersion());
    }
}
