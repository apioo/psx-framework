<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Tests\Data\Writer;

use PSX\Framework\Data\Writer\Html;
use PSX\Framework\Loader\ReverseRouter;
use PSX\Framework\Template\TemplateInterface;
use PSX\Http\MediaType;

/**
 * HtmlTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class HtmlTest extends TemplateAbstractTestCase
{
    protected function getWriter(TemplateInterface $template, ReverseRouter $router)
    {
        return new Html($template, $router);
    }

    public function testIsContentTypeSupported()
    {
        $template = $this->createMock('PSX\Framework\Template\TemplateInterface');
        $router   = $this->getMockBuilder('PSX\Framework\Loader\ReverseRouter')
            ->disableOriginalConstructor()
            ->getMock();

        $writer = new Html($template, $router);

        $this->assertTrue($writer->isContentTypeSupported(new MediaType('text/html')));
        $this->assertFalse($writer->isContentTypeSupported(new MediaType('application/xml')));
    }

    public function testGetContentType()
    {
        $template = $this->createMock('PSX\Framework\Template\TemplateInterface');
        $router   = $this->getMockBuilder('PSX\Framework\Loader\ReverseRouter')
            ->disableOriginalConstructor()
            ->getMock();

        $writer = new Html($template, $router);

        $this->assertEquals('text/html', $writer->getContentType());
    }
}
