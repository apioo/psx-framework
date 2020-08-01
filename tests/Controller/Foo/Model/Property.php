<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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
 */
class Property
{
    /**
     * @var \PSX\Framework\Tests\Controller\Foo\Model\Any
     */
    private $any;

    /**
     * @var array<string>
     */
    private $array;

    /**
     * @var array<\PSX\Framework\Tests\Controller\Foo\Model\Complex>
     */
    private $arrayComplex;

    /**
     * @var array<\PSX\Framework\Tests\Controller\Foo\Model\ChoiceA|\PSX\Framework\Tests\Controller\Foo\Model\ChoiceB>
     */
    private $arrayChoice;

    /**
     * @var boolean
     */
    private $boolean;

    /**
     * @var \PSX\Framework\Tests\Controller\Foo\Model\ChoiceA|\PSX\Framework\Tests\Controller\Foo\Model\ChoiceB
     */
    private $choice;

    /**
     * @var \PSX\Framework\Tests\Controller\Foo\Model\Complex
     */
    private $complex;

    /**
     * @var \PSX\DateTime\Date
     */
    private $date;

    /**
     * @var \PSX\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \PSX\DateTime\Duration
     */
    private $duration;

    /**
     * @var float
     */
    private $float;

    /**
     * @var integer
     */
    private $integer;

    /**
     * @var string
     */
    private $string;

    /**
     * @var \PSX\DateTime\Time
     */
    private $time;

    /**
     * @return Any
     */
    public function getAny(): ?Any
    {
        return $this->any;
    }

    /**
     * @param Any $any
     */
    public function setAny(Any $any): void
    {
        $this->any = $any;
    }

    /**
     * @return array
     */
    public function getArray(): ?array
    {
        return $this->array;
    }

    /**
     * @param array $array
     */
    public function setArray(array $array): void
    {
        $this->array = $array;
    }

    /**
     * @return array
     */
    public function getArrayComplex(): ?array
    {
        return $this->arrayComplex;
    }

    /**
     * @param array $arrayComplex
     */
    public function setArrayComplex(array $arrayComplex): void
    {
        $this->arrayComplex = $arrayComplex;
    }

    /**
     * @return array
     */
    public function getArrayChoice(): ?array
    {
        return $this->arrayChoice;
    }

    /**
     * @param array $arrayChoice
     */
    public function setArrayChoice(array $arrayChoice): void
    {
        $this->arrayChoice = $arrayChoice;
    }

    /**
     * @return bool
     */
    public function getBoolean(): ?bool
    {
        return $this->boolean;
    }

    /**
     * @param bool $boolean
     */
    public function setBoolean(bool $boolean): void
    {
        $this->boolean = $boolean;
    }

    /**
     * @return ChoiceA|ChoiceB
     */
    public function getChoice()
    {
        return $this->choice;
    }

    /**
     * @param ChoiceA|ChoiceB $choice
     */
    public function setChoice($choice): void
    {
        $this->choice = $choice;
    }

    /**
     * @return Complex
     */
    public function getComplex(): ?Complex
    {
        return $this->complex;
    }

    /**
     * @param Complex $complex
     */
    public function setComplex(Complex $complex): void
    {
        $this->complex = $complex;
    }

    /**
     * @return \PSX\DateTime\Date
     */
    public function getDate(): ?\PSX\DateTime\Date
    {
        return $this->date;
    }

    /**
     * @param \PSX\DateTime\Date $date
     */
    public function setDate(\PSX\DateTime\Date $date): void
    {
        $this->date = $date;
    }

    /**
     * @return \PSX\DateTime\DateTime
     */
    public function getDateTime(): ?\PSX\DateTime\DateTime
    {
        return $this->dateTime;
    }

    /**
     * @param \PSX\DateTime\DateTime $dateTime
     */
    public function setDateTime(\PSX\DateTime\DateTime $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    /**
     * @return \PSX\DateTime\Duration
     */
    public function getDuration(): ?\PSX\DateTime\Duration
    {
        return $this->duration;
    }

    /**
     * @param \PSX\DateTime\Duration $duration
     */
    public function setDuration(\PSX\DateTime\Duration $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return float
     */
    public function getFloat(): ?float
    {
        return $this->float;
    }

    /**
     * @param float $float
     */
    public function setFloat(float $float): void
    {
        $this->float = $float;
    }

    /**
     * @return int
     */
    public function getInteger(): ?int
    {
        return $this->integer;
    }

    /**
     * @param int $integer
     */
    public function setInteger(int $integer): void
    {
        $this->integer = $integer;
    }

    /**
     * @return string
     */
    public function getString(): ?string
    {
        return $this->string;
    }

    /**
     * @param string $string
     */
    public function setString(string $string): void
    {
        $this->string = $string;
    }

    /**
     * @return \PSX\DateTime\Time
     */
    public function getTime(): ?\PSX\DateTime\Time
    {
        return $this->time;
    }

    /**
     * @param \PSX\DateTime\Time $time
     */
    public function setTime(\PSX\DateTime\Time $time): void
    {
        $this->time = $time;
    }
}
