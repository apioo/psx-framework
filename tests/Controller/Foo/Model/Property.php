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

namespace PSX\Framework\Tests\Controller\Foo\Model;

use DateTime;
use DateInterval;

/**
 * Property
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Property
{
    /**
     * @Type("any<string>")
     */
    protected $any;

    /**
     * @Type("array<string>")
     */
    protected $array;

    /**
     * @Type("array<PSX\Framework\Tests\Controller\Foo\Model\Complex>")
     */
    protected $arrayComplex;

    /**
     * @Type("array<choice<a=PSX\Framework\Tests\Controller\Foo\Model\ChoiceA,b=PSX\Framework\Tests\Controller\Foo\Model\ChoiceB>>")
     */
    protected $arrayChoice;

    /**
     * @Type("boolean")
     */
    protected $boolean;

    /**
     * @Type("choice<a=PSX\Framework\Tests\Controller\Foo\Model\ChoiceA,b=PSX\Framework\Tests\Controller\Foo\Model\ChoiceB>")
     */
    protected $choice;

    /**
     * @Type("PSX\Framework\Tests\Controller\Foo\Model\Complex")
     */
    protected $complex;

    /**
     * @Type("date")
     */
    protected $date;

    /**
     * @Type("datetime")
     */
    protected $dateTime;

    /**
     * @Type("duration")
     */
    protected $duration;

    /**
     * @Type("float")
     */
    protected $float;

    /**
     * @Type("integer")
     */
    protected $integer;

    /**
     * @Type("string")
     */
    protected $string;

    /**
     * @Type("time")
     */
    protected $time;

    public function getAny()
    {
        return $this->any;
    }

    public function setAny($any)
    {
        $this->any = $any;
    }

    public function getArray()
    {
        return $this->array;
    }

    public function setArray(array $array)
    {
        $this->array = $array;
    }

    public function getArrayComplex()
    {
        return $this->arrayComplex;
    }

    public function setArrayComplex(array $arrayComplex)
    {
        $this->arrayComplex = $arrayComplex;
    }

    public function getArrayChoice()
    {
        return $this->arrayChoice;
    }

    public function setArrayChoice(array $arrayChoice)
    {
        $this->arrayChoice = $arrayChoice;
    }

    public function getBoolean()
    {
        return $this->boolean;
    }

    public function setBoolean($boolean)
    {
        $this->boolean = $boolean;
    }

    public function getChoice()
    {
        return $this->choice;
    }

    public function setChoice($choice)
    {
        $this->choice = $choice;
    }

    public function getComplex()
    {
        return $this->complex;
    }

    public function setComplex(Complex $complex)
    {
        $this->complex = $complex;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate(DateTime $date)
    {
        $this->date = $date;
    }

    public function getDateTime()
    {
        return $this->dateTime;
    }

    public function setDateTime(DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function setDuration(DateInterval $duration)
    {
        $this->duration = $duration;
    }

    public function getFloat()
    {
        return $this->float;
    }

    public function setFloat($float)
    {
        $this->float = $float;
    }

    public function getInteger()
    {
        return $this->integer;
    }

    public function setInteger($integer)
    {
        $this->integer = $integer;
    }

    public function getString()
    {
        return $this->string;
    }

    public function setString($string)
    {
        $this->string = $string;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function setTime(DateTime $time)
    {
        $this->time = $time;
    }
}
