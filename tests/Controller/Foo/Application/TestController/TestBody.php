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

namespace PSX\Framework\Tests\Controller\Foo\Application\TestController;

/**
 * TestBody
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestBody
{
    /**
     * @var string
     */
    private $foo;

    /**
     * @var string
     */
    private $title;

    /**
     * @var \PSX\Framework\Tests\Controller\Foo\Application\TestController\TestBody
     */
    protected $bar;

    /**
     * @var array<\PSX\Framework\Tests\Controller\Foo\Application\TestController\TestBody>
     */
    protected $entries;

    /**
     * @return string
     */
    public function getFoo(): string
    {
        return $this->foo;
    }

    /**
     * @param string $foo
     */
    public function setFoo(string $foo): void
    {
        $this->foo = $foo;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return TestBody
     */
    public function getBar(): TestBody
    {
        return $this->bar;
    }

    /**
     * @param TestBody $bar
     */
    public function setBar(TestBody $bar): void
    {
        $this->bar = $bar;
    }

    /**
     * @return array
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @param array $entries
     */
    public function setEntries(array $entries): void
    {
        $this->entries = $entries;
    }
}
