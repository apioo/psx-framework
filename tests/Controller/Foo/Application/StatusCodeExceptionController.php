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

namespace PSX\Framework\Tests\Controller\Foo\Application;

use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\PathParam;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Exception as StatusCode;

/**
 * StatusCodeExceptionController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class StatusCodeExceptionController extends ControllerAbstract
{
    #[Get]
    #[Path('/tests/status/:code')]
    #[PathParam('code', 'integer')]
    public function doGet(int $code): mixed
    {
        switch ($code) {
            case 301:
                throw new StatusCode\MovedPermanentlyException('http://google.com');
            case 302:
                throw new StatusCode\FoundException('http://google.com');
            case 303:
                throw new StatusCode\SeeOtherException('http://google.com');
            case 304:
                throw new StatusCode\NotModifiedException();
            case 307:
                throw new StatusCode\TemporaryRedirectException('http://google.com');
            case 400:
                throw new StatusCode\BadRequestException('foobar');
            case 401:
                throw new StatusCode\UnauthorizedException('foobar', 'Basic', array('realm' => 'foo'));
            case 403:
                throw new StatusCode\ForbiddenException('foobar');
            case 404:
                throw new StatusCode\NotFoundException('foobar');
            case 405:
                throw new StatusCode\MethodNotAllowedException('foobar', ['GET', 'POST']);
            case 406:
                throw new StatusCode\NotAcceptableException('foobar');
            case 409:
                throw new StatusCode\ConflictException('foobar');
            case 410:
                throw new StatusCode\GoneException('foobar');
            case 415:
                throw new StatusCode\UnsupportedMediaTypeException('foobar');
            case 500:
                throw new StatusCode\InternalServerErrorException('foobar');
            case 501:
                throw new StatusCode\NotImplementedException('foobar');
            case 503:
                throw new StatusCode\ServiceUnavailableException('foobar');
        }
    }
}
