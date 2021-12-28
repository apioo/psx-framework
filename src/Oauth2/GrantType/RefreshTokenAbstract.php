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

namespace PSX\Framework\Oauth2\GrantType;

use PSX\Framework\Oauth2\Credentials;
use PSX\Framework\Oauth2\GrantTypeInterface;
use PSX\Oauth2\AccessToken;
use PSX\Oauth2\Grant;
use PSX\Oauth2\Authorization\Exception\InvalidRequestException;
use PSX\Oauth2\GrantInterface;

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
