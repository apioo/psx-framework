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

namespace PSX\Framework\Filter;

use PSX\Http\Exception\UnauthorizedException;
use PSX\Http\FilterChainInterface;
use PSX\Http\FilterInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * OAuth2AuthenticationAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class OAuth2AuthenticationAbstract implements FilterInterface
{
    private ?string $realm;

    public function __construct(?string $realm = null)
    {
        $this->realm = $realm;
    }

    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain): void
    {
        $authorization = $request->getHeader('Authorization');
        if (!empty($authorization)) {
            $parts       = explode(' ', $authorization, 2);
            $type        = $parts[0] ?? null;
            $accessToken = $parts[1] ?? null;

            if ($type === 'Bearer' && !empty($accessToken)) {
                if ($this->accessAllowed($accessToken)) {
                    $this->onSuccess($response);

                    $filterChain->handle($request, $response);
                } else {
                    $this->onFailure($response);
                }
            } else {
                $this->onMissing($response);
            }
        } else {
            $this->onMissing($response);
        }
    }

    abstract public function accessAllowed(string $accessToken): bool;

    abstract public function onSuccess(ResponseInterface $response): void;

    public function onFailure(ResponseInterface $response): void
    {
        $params = [
            'realm' => $this->realm ?: 'psx',
        ];

        throw new UnauthorizedException('Invalid access token', 'Bearer', $params);
    }

    public function onMissing(ResponseInterface $response): void
    {
        $params = [
            'realm' => $this->realm ?: 'psx',
        ];

        throw new UnauthorizedException('Missing authorization header', 'Bearer', $params);
    }
}
