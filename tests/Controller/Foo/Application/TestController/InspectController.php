<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PHPUnit\Framework\Assert;
use PSX\Api\Attribute\Incoming;
use PSX\Data\Accessor;
use PSX\Data\ReaderInterface;
use PSX\Framework\Config\Config;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Loader\Context;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Http\Environment\HttpResponse;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Record\Record;
use PSX\Uri\Uri;
use PSX\Validate\Validate;
use PSX\Validate\Filter;

/**
 * InspectController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class InspectController extends ControllerAbstract
{
    #[Incoming(schema: TestBody::class)]
    protected function doPost(mixed $record, HttpContextInterface $context): mixed
    {
        Assert::assertEquals(null, $context->getUriFragment('foo'));
        Assert::assertEquals(null, $context->getHeader('foo'));
        Assert::assertEquals('bar', $context->getParameter('foo'));

        Assert::assertInstanceOf(TestBody::class, $record);
        Assert::assertEquals('bar', $record->getFoo());

        Assert::assertInstanceOf(Context::class, $this->context);
        Assert::assertEquals(self::class, $this->context->getSource());
        Assert::assertInstanceOf(Config::class, $this->config);

        return new HttpResponse(200, [], $record);
    }
}
