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

namespace PSX\Framework\Tests\Template\Engine;

use PSX\Framework\Template\Engine\Twig;
use PSX\Framework\Test\Environment;

/**
 * TwigTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TwigTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Twig_Environment')) {
            $this->markTestSkipped('Twig is not installed');
        }
    }

    public function testTransform()
    {
        $template = new Twig(
            Environment::getService('config')->get('psx_path_cache'),
            Environment::getService('config')->get('psx_debug')
        );

        $template->setDir(__DIR__ . '/../twig');
        $template->set('foo.twig.html');

        $this->assertTrue($template->hasFile());
        $this->assertTrue($template->isFileAvailable());
        $this->assertFalse($template->isAbsoluteFile());
        $this->assertEquals('foo.twig.html', $template->get());

        $template->assign('foo', 'bar');

        $content = $template->transform();

        $this->assertEquals('Hello bar', $content);
    }
}
