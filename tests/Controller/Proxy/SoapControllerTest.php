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

namespace PSX\Framework\Tests\Controller\Proxy;

use PSX\Framework\Test\ControllerDbTestCase;
use PSX\Framework\Test\Environment;

/**
 * SoapControllerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SoapControllerTest extends ControllerDbTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/../../table_fixture.xml');
    }

    public function testIndex()
    {
        $headers  = ['SOAPAction' => '/api#GET'];
        $response = $this->sendRequest('/soap', 'POST', $headers);
        $xml      = (string) $response->getBody();

        $expect = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <record xmlns="http://phpsx.org/2014/data" type="object">
      <entry type="array">
        <entry type="object">
          <id type="integer">4</id>
          <userId type="integer">3</userId>
          <title type="string">blub</title>
          <date type="date-time">2013-04-29T16:56:32Z</date>
        </entry>
        <entry type="object">
          <id type="integer">3</id>
          <userId type="integer">2</userId>
          <title type="string">test</title>
          <date type="date-time">2013-04-29T16:56:32Z</date>
        </entry>
        <entry type="object">
          <id type="integer">2</id>
          <userId type="integer">1</userId>
          <title type="string">bar</title>
          <date type="date-time">2013-04-29T16:56:32Z</date>
        </entry>
        <entry type="object">
          <id type="integer">1</id>
          <userId type="integer">1</userId>
          <title type="string">foo</title>
          <date type="date-time">2013-04-29T16:56:32Z</date>
        </entry>
      </entry>
    </record>
  </soap:Body>
</soap:Envelope>
XML;

        $this->assertEquals(200, $response->getStatusCode(), $xml);
        $this->assertEquals('text/xml', $response->getHeader('Content-Type'), $xml);
        $this->assertXmlStringEqualsXmlString($expect, $xml, $xml);
    }

    public function testPost()
    {
        $header = ['SOAPAction' => '/api#POST'];
        $body   = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
  <postRequest xmlns="http://phpsx.org/2014/data">
    <userId>3</userId>
    <title>test</title>
    <date>2013-04-29T16:56:32Z</date>
  </postRequest>
 </soap:Body>
</soap:Envelope>
XML;

        $response = $this->sendRequest('/soap', 'POST', $header, $body);
        $xml      = (string) $response->getBody();

        $expect = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
  <record type="object" xmlns="http://phpsx.org/2014/data">
   <success type="boolean">true</success>
   <message type="string">You have successful post a record</message>
  </record>
 </soap:Body>
</soap:Envelope>
XML;

        $this->assertEquals(201, $response->getStatusCode(), $xml);
        $this->assertEquals('text/xml', $response->getHeader('Content-Type'), $xml);
        $this->assertXmlStringEqualsXmlString($expect, $xml, $xml);
    }

    public function testInvalidMethod()
    {
        Environment::getService('config')->set('psx_debug', false);

        $headers  = ['SOAPAction' => '/api#GET'];
        $response = $this->sendRequest('/soap', 'GET', $headers);
        $xml      = (string) $response->getBody();

        $expect = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
  <soap:Fault>
   <faultcode>soap:Server</faultcode>
   <faultstring>Only POST requests are allowed</faultstring>
  </soap:Fault>
 </soap:Body>
</soap:Envelope>
XML;

        $this->assertEquals(405, $response->getStatusCode(), $xml);
        $this->assertEquals('text/xml', $response->getHeader('Content-Type'), $xml);
        $this->assertXmlStringEqualsXmlString($expect, $xml, $xml);
    }

    public function testInvalidMethodFragment()
    {
        Environment::getService('config')->set('psx_debug', false);

        $headers  = ['SOAPAction' => '/api#FOO'];
        $response = $this->sendRequest('/soap', 'POST', $headers);
        $xml      = (string) $response->getBody();

        $expect = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
  <soap:Fault>
   <faultcode>soap:Server</faultcode>
   <faultstring>Invalid request method</faultstring>
  </soap:Fault>
 </soap:Body>
</soap:Envelope>
XML;

        $this->assertEquals(400, $response->getStatusCode(), $xml);
        $this->assertEquals('text/xml', $response->getHeader('Content-Type'), $xml);
        $this->assertXmlStringEqualsXmlString($expect, $xml, $xml);
    }

    public function testExplicitAccept()
    {
        Environment::getService('config')->set('psx_debug', false);

        $headers  = ['SOAPAction' => '/api#GET'];
        $response = $this->sendRequest('/soap', 'POST', $headers);
        $xml      = (string) $response->getBody();

        $expect = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
  <record type="object" xmlns="http://phpsx.org/2014/data">
   <entry type="array">
    <entry type="object">
     <id type="integer">4</id>
     <userId type="integer">3</userId>
     <title type="string">blub</title>
     <date type="date-time">2013-04-29T16:56:32Z</date>
    </entry>
    <entry type="object">
     <id type="integer">3</id>
     <userId type="integer">2</userId>
     <title type="string">test</title>
     <date type="date-time">2013-04-29T16:56:32Z</date>
    </entry>
    <entry type="object">
     <id type="integer">2</id>
     <userId type="integer">1</userId>
     <title type="string">bar</title>
     <date type="date-time">2013-04-29T16:56:32Z</date>
    </entry>
    <entry type="object">
     <id type="integer">1</id>
     <userId type="integer">1</userId>
     <title type="string">foo</title>
     <date type="date-time">2013-04-29T16:56:32Z</date>
    </entry>
   </entry>
  </record>
 </soap:Body>
</soap:Envelope>
XML;

        $this->assertEquals(200, $response->getStatusCode(), $xml);
        $this->assertEquals('text/xml', $response->getHeader('Content-Type'), $xml);
        $this->assertXmlStringEqualsXmlString($expect, $xml, $xml);
    }

    public function testExplicitFormat()
    {
        Environment::getService('config')->set('psx_debug', false);

        $headers  = ['SOAPAction' => '/api#GET'];
        $response = $this->sendRequest('/soap?format=json', 'POST', $headers);
        $xml      = (string) $response->getBody();

        $expect = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
  <record type="object" xmlns="http://phpsx.org/2014/data">
   <entry type="array">
    <entry type="object">
     <id type="integer">4</id>
     <userId type="integer">3</userId>
     <title type="string">blub</title>
     <date type="date-time">2013-04-29T16:56:32Z</date>
    </entry>
    <entry type="object">
     <id type="integer">3</id>
     <userId type="integer">2</userId>
     <title type="string">test</title>
     <date type="date-time">2013-04-29T16:56:32Z</date>
    </entry>
    <entry type="object">
     <id type="integer">2</id>
     <userId type="integer">1</userId>
     <title type="string">bar</title>
     <date type="date-time">2013-04-29T16:56:32Z</date>
    </entry>
    <entry type="object">
     <id type="integer">1</id>
     <userId type="integer">1</userId>
     <title type="string">foo</title>
     <date type="date-time">2013-04-29T16:56:32Z</date>
    </entry>
   </entry>
  </record>
 </soap:Body>
</soap:Envelope>
XML;

        $this->assertEquals(200, $response->getStatusCode(), $xml);
        $this->assertEquals('text/xml', $response->getHeader('Content-Type'), $xml);
        $this->assertXmlStringEqualsXmlString($expect, $xml, $xml);
    }

    protected function getPaths()
    {
        return array(
            [['GET', 'POST'], '/soap', 'PSX\Framework\Controller\Proxy\SoapController'],
            [['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiController'],
        );
    }
}
