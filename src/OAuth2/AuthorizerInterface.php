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

use PSX\Uri\Url;

/**
 * AuthorizerInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface AuthorizerInterface
{
    /**
     * Returns whether the user has authorized the client_id
     */
    public function hasGrant(AccessRequest $request): bool;

    /**
     * Generates an authorization code which is assigned to the request
     */
    public function generateCode(AccessRequest $request): string;

    /**
     * This method is called if no redirect_uri was set. You can overwrite this method if it is possible to get a
     * callback from another source i.e. it was attached to the client or an app
     */
    public function getCallback(string $clientId): ?Url;
}
