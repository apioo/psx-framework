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
    private ?string $redirectUri;
    private ?string $scope;
    private ?string $state;

    public function __construct(string $clientId, ?string $redirectUri = null, ?string $scope = null, ?string $state = null)
    {
        $this->clientId = $clientId;
        $this->scope = $scope;
        $this->state = $state;

        $this->setRedirectUri($redirectUri);
    }

    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }
    
    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setRedirectUri(?string $redirectUri): void
    {
        if (!empty($redirectUri)) {
            $this->redirectUri = new Url($redirectUri);
        } else {
            $this->redirectUri = null;
        }
    }
    
    public function getRedirectUri(): ?string
    {
        return $this->redirectUri;
    }

    public function hasRedirectUri(): bool
    {
        return $this->redirectUri instanceof Url;
    }

    public function setScope(string $scope): void
    {
        $this->scope = $scope;
    }
    
    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
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
