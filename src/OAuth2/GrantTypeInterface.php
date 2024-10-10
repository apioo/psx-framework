<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\OAuth2;

use PSX\OAuth2\AccessToken;
use PSX\OAuth2\GrantInterface;

/**
 * GrantTypeInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface GrantTypeInterface
{
    public const TYPE_AUTHORIZATION_CODE = 'authorization_code';
    public const TYPE_CLIENT_CREDENTIALS = 'client_credentials';
    public const TYPE_IMPLICIT           = 'implicit';
    public const TYPE_PASSWORD           = 'password';
    public const TYPE_REFRESH_TOKEN      = 'refresh_token';

    /**
     * Returns the name of this grant type
     */
    public function getType(): string;

    /**
     * Returns an access token based on the credentials and request parameters.
     * In some grant types the credentials can be null
     */
    public function generateAccessToken(?Credentials $credentials, GrantInterface $grant): AccessToken;
}
