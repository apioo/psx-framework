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

namespace PSX\Framework\Tests\Filter;

use PHPUnit\Framework\TestCase;
use PSX\Framework\Filter\OAuth2AuthenticationAbstract;
use PSX\Http\Exception\BadRequestException;
use PSX\Http\Exception\UnauthorizedException;
use PSX\Http\Filter\FilterChain;
use PSX\Http\FilterChainInterface;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;
use PSX\Oauth2\AccessToken;
use PSX\Oauth2\Client;
use PSX\Uri\Url;

/**
 * OAuth2TestFilter
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OAuth2TestFilter extends OAuth2AuthenticationAbstract
{
    public const ACCESS_TOKEN = '2YotnFZFEjr1zCsicMWpAA';

    private \Closure $validateCallback;
    private ?\Closure $successCallback;

    public function __construct(\Closure $validateCallback, ?\Closure $successCallback = null)
    {
        parent::__construct('psx');

        $this->validateCallback = $validateCallback;
        $this->successCallback = $successCallback;
    }

    public function accessAllowed(string $accessToken): bool
    {
        return call_user_func_array($this->validateCallback, [$accessToken]);
    }

    public function onSuccess(ResponseInterface $response): void
    {
        if ($this->successCallback !== null) {
            call_user_func_array($this->successCallback, [$response]);
        }
    }
}
