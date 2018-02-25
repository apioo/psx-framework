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

namespace PSX\Framework\Tests\Controller\Foo\Application\TestApi;

use PSX\Framework\Controller\ApiAbstract;
use PSX\Framework\Tests\Controller\Foo\Schema\NestedEntry;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Record\RecordInterface;
use PSX\Data\Validator\Property;
use PSX\Data\Validator\Validator;
use PSX\Validate\Filter;
use PSX\Validate\Validate;

/**
 * ValidateController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValidateController extends ApiAbstract
{
    /**
     * @Inject
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    /**
     * @Inject
     * @var \PSX\Schema\SchemaManager
     */
    protected $schemaManager;

    public function onGet(RequestInterface $request, ResponseInterface $response)
    {
        $data = [
            'foo' => 'bar'
        ];

        $this->responseWriter->setBody($response, $data, $request);
    }

    public function onPost(RequestInterface $request, ResponseInterface $response)
    {
        $schema    = $this->schemaManager->getSchema(NestedEntry::class);
        $validator = new Validator([
            new Property('/title', Validate::TYPE_STRING, [new Filter\Length(3, 8)]),
            new Property('/author/name', Validate::TYPE_STRING, [new Filter\Length(3, 8)]),
        ]);

        $data = $this->requestReader->getBodyAs($request, $schema, $validator);

        $this->testCase->assertInstanceOf(RecordInterface::class, $data);

        $data = [
            'success' => true,
        ];

        $this->responseWriter->setBody($response, $data, $request);
    }
}
