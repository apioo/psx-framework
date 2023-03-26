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

namespace PSX\Framework\OAuth2\GrantType;

use PSX\Framework\OAuth2\Credentials;
use PSX\Framework\OAuth2\GrantTypeInterface;
use PSX\OAuth2\AccessToken;
use PSX\OAuth2\Grant;
use PSX\OAuth2\Exception\InvalidRequestException;
use PSX\OAuth2\GrantInterface;

/**
 * RefreshTokenAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class RefreshTokenAbstract implements GrantTypeInterface
{
    public function getType(): string
    {
        return self::TYPE_REFRESH_TOKEN;
    }

    public function generateAccessToken(?Credentials $credentials, GrantInterface $grant): AccessToken
    {
        if ($credentials === null) {
            throw new InvalidRequestException('Credentials not available');
        }

        if (!$grant instanceof Grant\RefreshToken) {
            throw new InvalidRequestException('Provided an invalid grant');
        }

        return $this->generate($credentials, $grant);
    }

    abstract protected function generate(Credentials $credentials, Grant\RefreshToken $grant);
}
