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

namespace PSX\Framework\Tests\OAuth2;

use PSX\Framework\Oauth2\AccessRequest;
use PSX\Framework\OAuth2\AuthorizerInterface;
use PSX\Uri\Url;

/**
 * TestAuthorizer
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class TestAuthorizer implements AuthorizerInterface
{
    private static bool $hasGrant;
    private static string $code;

    public static function setHasGrant(bool $hasGrant): void
    {
        self::$hasGrant = $hasGrant;
    }

    public static function setCode(string $code): void
    {
        self::$code = $code;
    }

    public function hasGrant(AccessRequest $request): bool
    {
        return self::$hasGrant;
    }

    public function generateCode(AccessRequest $request): string
    {
        return self::$code;
    }

    public function getCallback(string $clientId): ?Url
    {
        return null;
    }
}