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

namespace PSX\Framework\Tests\Controller\Foo\Model;

use DateInterval;
use DateTime;

/**
 * Property
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @AdditionalProperties(false)
 */
class Property
{
    /**
     * @Ref("PSX\Framework\Tests\Controller\Foo\Model\Any")
     */
    protected $any;

    /**
     * @Type("array")
     * @Items(@Schema(type="string"))
     */
    protected $array;

    /**
     * @Type("array")
     * @Items(@Ref("PSX\Framework\Tests\Controller\Foo\Model\Complex"))
     */
    protected $arrayComplex;

    /**
     * @Type("array")
     * @Items(@Schema(oneOf={@Ref("PSX\Framework\Tests\Controller\Foo\Model\ChoiceA"), @Ref("PSX\Framework\Tests\Controller\Foo\Model\ChoiceB")}))
     */
    protected $arrayChoice;

    /**
     * @Type("boolean")
     */
    protected $boolean;

    /**
     * @OneOf(@Ref("PSX\Framework\Tests\Controller\Foo\Model\ChoiceA"), @Ref("PSX\Framework\Tests\Controller\Foo\Model\ChoiceB"))
     */
    protected $choice;

    /**
     * @Ref("PSX\Framework\Tests\Controller\Foo\Model\Complex")
     */
    protected $complex;

    /**
     * @Type("string")
     * @Format("date")
     */
    protected $date;

    /**
     * @Type("string")
     * @Format("date-time")
     */
    protected $dateTime;

    /**
     * @Type("string")
     * @Format("duration")
     */
    protected $duration;

    /**
     * @Type("number")
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
     * @Type("string")
     * @Format("time")
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
