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

namespace PSX\Framework\Tests\Controller\Foo\Application;

use Psr\Http\Message\StreamInterface;
use PSX\Api\Attribute\Body;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Post;
use PSX\Data\Body\Form;
use PSX\Data\Body\Json;
use PSX\Data\Body\Multipart;
use PSX\Framework\Controller\ControllerAbstract;

/**
 * ContentTypeController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ContentTypeController extends ControllerAbstract
{
    #[Post]
    #[Path('/tests/content_type/binary')]
    public function binary(#[Body] StreamInterface $body): StreamInterface
    {
        return $body;
    }

    #[Post]
    #[Path('/tests/content_type/form')]
    public function form(#[Body] Form $body): Form
    {
        return $body;
    }

    #[Post]
    #[Path('/tests/content_type/json')]
    public function json(#[Body] Json $body): Json
    {
        return $body;
    }

    #[Post]
    #[Path('/tests/content_type/multipart')]
    public function multipart(#[Body] Multipart $body): Multipart
    {
        return $body;
    }

    #[Post]
    #[Path('/tests/content_type/text')]
    public function text(#[Body] string $body): string
    {
        return $body;
    }
}
