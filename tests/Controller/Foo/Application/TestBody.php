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

namespace PSX\Framework\Tests\Controller\Foo\Application;

use DOMDocument;
use PSX\Data\Accessor;
use PSX\Data\ReaderInterface;
use PSX\Data\WriterInterface;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Http\Body;
use PSX\Framework\Loader\Context;
use PSX\Http\Stream\FileStream;
use PSX\Record\Record;
use PSX\Uri\Url;
use PSX\Validate\Filter;
use PSX\Validate\Validate;
use SimpleXMLElement;

/**
 * TestController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestBody
{
    /**
     * @Type("string")
     */
    protected $foo;

    /**
     * @Type("string")
     */
    protected $title;

    /**
     * @Ref("\PSX\Framework\Tests\Controller\Foo\Application\TestBody")
     */
    protected $bar;

    /**
     * @Type("array")
     * @Items(@Ref("\PSX\Framework\Tests\Controller\Foo\Application\TestBody"))
     */
    protected $entries;

    public function getFoo()
    {
        return $this->foo;
    }

    public function setFoo($foo)
    {
        $this->foo = $foo;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function setBar(TestBody $bar)
    {
        $this->bar = $bar;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function setEntries(array $entries)
    {
        $this->entries = $entries;
    }
}
