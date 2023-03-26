<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\DateTime\LocalDate;
use PSX\DateTime\LocalDateTime;
use PSX\DateTime\LocalTime;
use PSX\DateTime\Period;

/**
 * Property
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Property
{
    private ?Any $any = null;

    /**
     * @var array<string>
     */
    private ?array $array = null;

    /**
     * @var array<Complex>
     */
    private ?array $arrayComplex = null;

    /**
     * @var array<ChoiceA|ChoiceB>
     */
    private ?array $arrayChoice = null;

    private ?bool $boolean = null;
    private ChoiceA|ChoiceB|null $choice = null;
    private ?Complex $complex = null;
    private ?LocalDate $date = null;
    private ?LocalDateTime $dateTime = null;
    private ?Period $duration = null;
    private ?float $float = null;
    private ?int $integer = null;
    private ?string $string = null;
    private ?LocalTime $time = null;

    public function getAny(): ?Any
    {
        return $this->any;
    }

    public function setAny(Any $any): void
    {
        $this->any = $any;
    }

    public function getArray(): ?array
    {
        return $this->array;
    }

    public function setArray(array $array): void
    {
        $this->array = $array;
    }

    public function getArrayComplex(): ?array
    {
        return $this->arrayComplex;
    }

    public function setArrayComplex(array $arrayComplex): void
    {
        $this->arrayComplex = $arrayComplex;
    }

    public function getArrayChoice(): ?array
    {
        return $this->arrayChoice;
    }

    public function setArrayChoice(array $arrayChoice): void
    {
        $this->arrayChoice = $arrayChoice;
    }

    public function getBoolean(): ?bool
    {
        return $this->boolean;
    }

    public function setBoolean(bool $boolean): void
    {
        $this->boolean = $boolean;
    }

    public function getChoice(): ChoiceA|ChoiceB|null
    {
        return $this->choice;
    }

    public function setChoice(ChoiceA|ChoiceB $choice): void
    {
        $this->choice = $choice;
    }

    public function getComplex(): ?Complex
    {
        return $this->complex;
    }

    public function setComplex(Complex $complex): void
    {
        $this->complex = $complex;
    }

    public function getDate(): ?LocalDate
    {
        return $this->date;
    }

    public function setDate(LocalDate $date): void
    {
        $this->date = $date;
    }

    public function getDateTime(): ?LocalDateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(LocalDateTime $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    public function getDuration(): ?Period
    {
        return $this->duration;
    }

    public function setDuration(Period $duration): void
    {
        $this->duration = $duration;
    }

    public function getFloat(): ?float
    {
        return $this->float;
    }

    public function setFloat(float $float): void
    {
        $this->float = $float;
    }

    public function getInteger(): ?int
    {
        return $this->integer;
    }

    public function setInteger(int $integer): void
    {
        $this->integer = $integer;
    }

    public function getString(): ?string
    {
        return $this->string;
    }

    public function setString(string $string): void
    {
        $this->string = $string;
    }

    public function getTime(): ?LocalTime
    {
        return $this->time;
    }

    public function setTime(LocalTime $time): void
    {
        $this->time = $time;
    }
}
