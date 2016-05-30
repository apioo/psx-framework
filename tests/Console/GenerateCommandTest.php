<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Framework\Tests\Console;

use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;

/**
 * GenerateCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GenerateCommandTest extends ControllerTestCase
{
    public function testCommand()
    {
        $command = $this->getMockBuilder('PSX\Framework\Console\GenerateCommand')
            ->setMethods(array('makeDir', 'writeFile', 'isDir', 'isFile'))
            ->getMock();

        $command->expects($this->at(0))
            ->method('isDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'))
            ->will($this->returnValue(false));

        $command->expects($this->at(1))
            ->method('makeDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'));

        $command->expects($this->at(2))
            ->method('isFile')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Endpoint.php'))
            ->will($this->returnValue(false));

        $command->expects($this->at(3))
            ->method('writeFile')
            ->with(
                $this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Endpoint.php'),
                $this->callback(function ($source) {
                    $this->assertSource($this->getExpectedEndpointSource(), $source);
                    return true;
                })
            );

        $command->expects($this->at(4))
            ->method('isFile')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Schema.php'))
            ->will($this->returnValue(false));

        $command->expects($this->at(5))
            ->method('writeFile')
            ->with(
                $this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Schema.php'),
                $this->callback(function ($source) {
                    $this->assertSource($this->getExpectedSchemaSource(), $source);
                    return true;
                })
            );

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'file'      => __DIR__ . '/../Controller/Foo/Resource/property.raml',
            'path'      =>'/api/{id}',
            'namespace' => 'Acme\Foo',
        ));
    }

    protected function assertSource($expect, $actual)
    {
        $expect = str_replace(array("\r\n", "\n", "\r"), "\n", $expect);
        $actual = str_replace(array("\r\n", "\n", "\r"), "\n", $actual);

        $this->assertEquals($expect, $actual, $actual);
    }

    protected function getExpectedEndpointSource()
    {
        return <<<'PHP'
<?php

namespace Acme\Foo;

use PSX\Framework\Controller\SchemaApiAbstract;
/**
 * @PathParam(name="id", type="integer")
 */
class Endpoint extends SchemaApiAbstract
{
    /**
     * @QueryParam(name="type", type="integer")
     * @Outgoing(code=200, schema="Acme\Foo\Complex148d238a")
     */
    public function doGet($record)
    {
    }
    /**
     * @Incoming(schema="Acme\Foo\Complex148d238a")
     * @Outgoing(code=200, schema="Acme\Foo\Complex148d238a")
     */
    public function doPost($record)
    {
    }
}
PHP;
    }

    protected function getExpectedSchemaSource()
    {
        return <<<'PHP'
<?php

namespace Acme\Foo;

/**
 * @AdditionalProperties("string")
 */
class Complex5525537f extends \ArrayObject
{
}
/**
 * @Title("a")
 * @AdditionalProperties(false)
 */
class Complex60bd1bf9
{
    /**
     * @Key("foo")
     * @Type("string")
     */
    public $foo;
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }
    public function getFoo()
    {
        return $this->foo;
    }
}
/**
 * @Title("choiceB")
 * @AdditionalProperties(false)
 */
class Complex2b8d1694
{
    /**
     * @Key("bar")
     * @Type("string")
     */
    public $bar;
    public function setBar($bar)
    {
        $this->bar = $bar;
    }
    public function getBar()
    {
        return $this->bar;
    }
}
/**
 * @AdditionalProperties(false)
 */
class Complex148d238a
{
    /**
     * @Key("any")
     * @Type("Acme\Foo\Complex5525537f")
     */
    public $any;
    /**
     * @Key("array")
     * @Type("array<string>")
     */
    public $array;
    /**
     * @Key("arrayComplex")
     * @Type("array<Acme\Foo\Complex60bd1bf9>")
     */
    public $arrayComplex;
    /**
     * @Key("arrayChoice")
     * @Type("array<choice<Acme\Foo\Complex60bd1bf9,Acme\Foo\Complex2b8d1694>>")
     */
    public $arrayChoice;
    /**
     * @Key("boolean")
     * @Type("boolean")
     */
    public $boolean;
    /**
     * @Key("choice")
     * @Type("choice<Acme\Foo\Complex60bd1bf9,Acme\Foo\Complex2b8d1694>")
     */
    public $choice;
    /**
     * @Key("complex")
     * @Type("Acme\Foo\Complex60bd1bf9")
     */
    public $complex;
    /**
     * @Key("date")
     * @Type("date")
     */
    public $date;
    /**
     * @Key("dateTime")
     * @Type("dateTime")
     */
    public $dateTime;
    /**
     * @Key("duration")
     * @Type("duration")
     */
    public $duration;
    /**
     * @Key("float")
     * @Type("float")
     */
    public $float;
    /**
     * @Key("integer")
     * @Type("integer")
     */
    public $integer;
    /**
     * @Key("string")
     * @Type("string")
     */
    public $string;
    /**
     * @Key("time")
     * @Type("time")
     */
    public $time;
    public function setAny($any)
    {
        $this->any = $any;
    }
    public function getAny()
    {
        return $this->any;
    }
    public function setArray($array)
    {
        $this->array = $array;
    }
    public function getArray()
    {
        return $this->array;
    }
    public function setArrayComplex($arrayComplex)
    {
        $this->arrayComplex = $arrayComplex;
    }
    public function getArrayComplex()
    {
        return $this->arrayComplex;
    }
    public function setArrayChoice($arrayChoice)
    {
        $this->arrayChoice = $arrayChoice;
    }
    public function getArrayChoice()
    {
        return $this->arrayChoice;
    }
    public function setBoolean($boolean)
    {
        $this->boolean = $boolean;
    }
    public function getBoolean()
    {
        return $this->boolean;
    }
    public function setChoice($choice)
    {
        $this->choice = $choice;
    }
    public function getChoice()
    {
        return $this->choice;
    }
    public function setComplex($complex)
    {
        $this->complex = $complex;
    }
    public function getComplex()
    {
        return $this->complex;
    }
    public function setDate($date)
    {
        $this->date = $date;
    }
    public function getDate()
    {
        return $this->date;
    }
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    }
    public function getDateTime()
    {
        return $this->dateTime;
    }
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }
    public function getDuration()
    {
        return $this->duration;
    }
    public function setFloat($float)
    {
        $this->float = $float;
    }
    public function getFloat()
    {
        return $this->float;
    }
    public function setInteger($integer)
    {
        $this->integer = $integer;
    }
    public function getInteger()
    {
        return $this->integer;
    }
    public function setString($string)
    {
        $this->string = $string;
    }
    public function getString()
    {
        return $this->string;
    }
    public function setTime($time)
    {
        $this->time = $time;
    }
    public function getTime()
    {
        return $this->time;
    }
}
/**
 * @Title("root")
 * @AdditionalProperties(false)
 */
class Complex81125937
{
    /**
     * @Key("getResponse")
     * @Type("Acme\Foo\Complex148d238a")
     */
    public $getResponse;
    /**
     * @Key("postRequest")
     * @Type("Acme\Foo\Complex148d238a")
     */
    public $postRequest;
    /**
     * @Key("postResponse")
     * @Type("Acme\Foo\Complex148d238a")
     */
    public $postResponse;
    public function setGetResponse($getResponse)
    {
        $this->getResponse = $getResponse;
    }
    public function getGetResponse()
    {
        return $this->getResponse;
    }
    public function setPostRequest($postRequest)
    {
        $this->postRequest = $postRequest;
    }
    public function getPostRequest()
    {
        return $this->postRequest;
    }
    public function setPostResponse($postResponse)
    {
        $this->postResponse = $postResponse;
    }
    public function getPostResponse()
    {
        return $this->postResponse;
    }
}
PHP;
    }
}
