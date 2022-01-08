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

namespace PSX\Framework\Oauth2;

use PSX\Uri\Url;

/**
 * AccessRequest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AccessRequest
{
    private string $clientId;
    private ?Url $redirectUri;
    private ?string $scope;
    private ?string $state;

    public function __construct(string $clientId, ?Url $redirectUri = null, ?string $scope = null, ?string $state = null)
    {
        $this->clientId = $clientId;
        $this->redirectUri = $redirectUri;
        $this->scope = $scope;
        $this->state = $state;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getRedirectUri(): ?Url
    {
        return $this->redirectUri;
    }

    public function hasRedirectUri(): bool
    {
        return $this->redirectUri instanceof Url;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function hasState(): bool
    {
        return !empty($this->state);
    }
}
