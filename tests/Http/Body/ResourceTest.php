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

namespace PSX\Framework\Tests\Http\Body;

use PSX\Framework\Http\Body\Body;
use PSX\Framework\Http\Body\File;
use PSX\Framework\Http\Body\Json;
use PSX\Framework\Http\Body\Multipart;
use PSX\Framework\Http\Body\Resource;
use PSX\Http\Response;

/**
 * ResourceTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResourceTest extends \PHPUnit_Framework_TestCase
{
    public function testWriteTo()
    {
        $handle = fopen('php://memory', 'r+');
        fwrite($handle, 'foobar');

        $response = new Response(200);

        $body = new Resource($handle);
        $body->writeTo($response);

        $this->assertEquals(['content-type' => ['application/octet-stream']], $response->getHeaders());
        $this->assertEquals('foobar', $response->getBody()->__toString());
    }
}
