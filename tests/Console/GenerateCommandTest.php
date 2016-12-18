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
        $expect = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $expect);
        $actual = str_replace(array("\r\n", "\n", "\r"), "\n", $actual);
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

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
     * @Outgoing(code=200, schema="Acme\Foo\ObjectId")
     */
    public function doGet($record)
    {
    }
    /**
     * @Incoming(schema="Acme\Foo\ObjectId")
     * @Outgoing(code=200, schema="Acme\Foo\ObjectId")
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
 * @AdditionalProperties(false)
 */
class ObjectId
{
    /**
     * @Key("foo")
     * @Type("string")
     */
    protected $foo;
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
 * @AdditionalProperties(@Schema(type="string"))
 */
class ObjectId extends \ArrayObject
{
}
/**
 * @AdditionalProperties(false)
 */
class ObjectId
{
    /**
     * @Key("foo")
     * @Type("string")
     */
    protected $foo;
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
 * @AdditionalProperties(@Schema(type="string"))
 */
class ObjectId extends \ArrayObject
{
}
/**
 * @AdditionalProperties(false)
 */
class ObjectId
{
    /**
     * @Key("foo")
     * @Type("string")
     */
    protected $foo;
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
class ChoiceB
{
    /**
     * @Key("bar")
     * @Type("string")
     */
    protected $bar;
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
 * @Title("choiceA")
 * @AdditionalProperties(false)
 */
class ChoiceA
{
    /**
     * @Key("foo")
     * @Type("string")
     */
    protected $foo;
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
 * @Title("a")
 */
class A
{
    /**
     * @Key("foo")
     * @Type("string")
     */
    protected $foo;
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
 * @AdditionalProperties(@Schema(type="string"))
 */
class ObjectId extends \ArrayObject
{
}
/**
 */
class ObjectId
{
    /**
     * @Key("any")
     * @Ref("Acme\Foo\ObjectId")
     */
    protected $any;
    /**
     * @Key("array")
     * @Type("array")
     * @Items(@Schema(type="string"))
     */
    protected $array;
    /**
     * @Key("arrayComplex")
     * @Type("array")
     * @Items(@Ref("Acme\Foo\A"))
     */
    protected $arrayComplex;
    /**
     * @Key("arrayChoice")
     * @Type("array")
     * @Items(@Schema(oneOf={@Ref("Acme\Foo\ChoiceA"), @Ref("Acme\Foo\ChoiceB")}))
     */
    protected $arrayChoice;
    /**
     * @Key("boolean")
     * @Type("boolean")
     */
    protected $boolean;
    /**
     * @Key("choice")
     * @OneOf(@Ref("Acme\Foo\ChoiceA"), @Ref("Acme\Foo\ChoiceB"))
     */
    protected $choice;
    /**
     * @Key("complex")
     * @Ref("Acme\Foo\ObjectId")
     */
    protected $complex;
    /**
     * @Key("date")
     * @Type("string")
     * @Format("date")
     */
    protected $date;
    /**
     * @Key("dateTime")
     * @Type("string")
     * @Format("date-time")
     */
    protected $dateTime;
    /**
     * @Key("duration")
     * @Type("string")
     * @Format("duration")
     */
    protected $duration;
    /**
     * @Key("float")
     * @Type("number")
     */
    protected $float;
    /**
     * @Key("integer")
     * @Type("integer")
     */
    protected $integer;
    /**
     * @Key("string")
     * @Type("string")
     */
    protected $string;
    /**
     * @Key("time")
     * @Type("string")
     * @Format("time")
     */
    protected $time;
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
 */
class ObjectId
{
    /**
     * @Key("any")
     * @Ref("Acme\Foo\ObjectId")
     */
    protected $any;
    /**
     * @Key("array")
     * @Type("array")
     * @Items(@Schema(type="string"))
     */
    protected $array;
    /**
     * @Key("arrayComplex")
     * @Type("array")
     * @Items(@Ref("Acme\Foo\A"))
     */
    protected $arrayComplex;
    /**
     * @Key("arrayChoice")
     * @Type("array")
     * @Items(@Schema(oneOf={@Ref("Acme\Foo\ChoiceA"), @Ref("Acme\Foo\ChoiceB")}))
     */
    protected $arrayChoice;
    /**
     * @Key("boolean")
     * @Type("boolean")
     */
    protected $boolean;
    /**
     * @Key("choice")
     * @OneOf(@Ref("Acme\Foo\ChoiceA"), @Ref("Acme\Foo\ChoiceB"))
     */
    protected $choice;
    /**
     * @Key("complex")
     * @Ref("Acme\Foo\ObjectId")
     */
    protected $complex;
    /**
     * @Key("date")
     * @Type("string")
     * @Format("date")
     */
    protected $date;
    /**
     * @Key("dateTime")
     * @Type("string")
     * @Format("date-time")
     */
    protected $dateTime;
    /**
     * @Key("duration")
     * @Type("string")
     * @Format("duration")
     */
    protected $duration;
    /**
     * @Key("float")
     * @Type("number")
     */
    protected $float;
    /**
     * @Key("integer")
     * @Type("integer")
     */
    protected $integer;
    /**
     * @Key("string")
     * @Type("string")
     */
    protected $string;
    /**
     * @Key("time")
     * @Type("string")
     * @Format("time")
     */
    protected $time;
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
 */
class ObjectId
{
    /**
     * @Key("any")
     * @Ref("Acme\Foo\ObjectId")
     */
    protected $any;
    /**
     * @Key("array")
     * @Type("array")
     * @Items(@Schema(type="string"))
     */
    protected $array;
    /**
     * @Key("arrayComplex")
     * @Type("array")
     * @Items(@Ref("Acme\Foo\A"))
     */
    protected $arrayComplex;
    /**
     * @Key("arrayChoice")
     * @Type("array")
     * @Items(@Schema(oneOf={@Ref("Acme\Foo\ChoiceA"), @Ref("Acme\Foo\ChoiceB")}))
     */
    protected $arrayChoice;
    /**
     * @Key("boolean")
     * @Type("boolean")
     */
    protected $boolean;
    /**
     * @Key("choice")
     * @OneOf(@Ref("Acme\Foo\ChoiceA"), @Ref("Acme\Foo\ChoiceB"))
     */
    protected $choice;
    /**
     * @Key("complex")
     * @Ref("Acme\Foo\ObjectId")
     */
    protected $complex;
    /**
     * @Key("date")
     * @Type("string")
     * @Format("date")
     */
    protected $date;
    /**
     * @Key("dateTime")
     * @Type("string")
     * @Format("date-time")
     */
    protected $dateTime;
    /**
     * @Key("duration")
     * @Type("string")
     * @Format("duration")
     */
    protected $duration;
    /**
     * @Key("float")
     * @Type("number")
     */
    protected $float;
    /**
     * @Key("integer")
     * @Type("integer")
     */
    protected $integer;
    /**
     * @Key("string")
     * @Type("string")
     */
    protected $string;
    /**
     * @Key("time")
     * @Type("string")
     * @Format("time")
     */
    protected $time;
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
 */
class Root
{
    /**
     * @Key("getResponse")
     * @Ref("Acme\Foo\ObjectId")
     */
    protected $getResponse;
    /**
     * @Key("postRequest")
     * @Ref("Acme\Foo\ObjectId")
     */
    protected $postRequest;
    /**
     * @Key("postResponse")
     * @Ref("Acme\Foo\ObjectId")
     */
    protected $postResponse;
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
