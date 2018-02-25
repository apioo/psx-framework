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

namespace PSX\Framework\Tests\Http\Body;

use PSX\Framework\Http\Body\Resource;
use PSX\Framework\Http\Body\Xml;
use PSX\Http\Response;
use PSX\Http\Stream\StringStream;

/**
 * XmlTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XmlTest extends \PHPUnit_Framework_TestCase
{
    public function testWriteTo()
    {
        $response = new Response(200);

        $body = new Xml($this->getXml());
        $body->writeTo($response);

        $this->assertEquals(['content-type' => ['application/xml']], $response->getHeaders());
        $this->assertXmlStringEqualsXmlString($this->getXml(), $response->getBody()->__toString());
    }

    public function testWriteToDomDocument()
    {
        $dom = new \DOMDocument();
        $dom->loadXML($this->getXml());

        $response = new Response(200);

        $body = new Xml($dom);
        $body->writeTo($response);

        $this->assertEquals(['content-type' => ['application/xml']], $response->getHeaders());
        $this->assertXmlStringEqualsXmlString($this->getXml(), $response->getBody()->__toString());
    }

    public function testWriteToSimpleXMLElement()
    {
        $response = new Response(200);

        $body = new Xml(new \SimpleXMLElement($this->getXml()));
        $body->writeTo($response);

        $this->assertEquals(['content-type' => ['application/xml']], $response->getHeaders());
        $this->assertXmlStringEqualsXmlString($this->getXml(), $response->getBody()->__toString());
    }

    private function getXml()
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<foo>
    <bar>foo</bar>
</foo>
XML;
    }
}
