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

namespace PSX\Framework\Controller\OAuth2;

use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Post;
use PSX\Api\Attribute\QueryParam;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Model\Passthru;
use PSX\Framework\OAuth2\CallbackInterface;
use PSX\Oauth2\Authorization\Exception\ErrorExceptionAbstract;
use PSX\Oauth2\AuthorizationAbstract;
use PSX\Oauth2\Grant;
use RuntimeException;

/**
 * CallbackController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CallbackController extends ControllerAbstract
{
    private CallbackInterface $callback;

    public function __construct(CallbackInterface $callback)
    {
        $this->callback = $callback;
    }

    #[Get]
    #[Path('/authorization/callback')]
    #[QueryParam('error', 'string')]
    #[QueryParam('error_description', 'string')]
    #[QueryParam('code', 'string')]
    #[QueryParam('state', 'string')]
    #[Incoming(schema: Passthru::class)]
    #[Outgoing(code: 200, schema: Passthru::class)]
    public function doGet(?string $error, ?string $error_description, ?string $code, ?string $state): mixed
    {
        return $this->execute($error, $error_description, $code, $state);
    }

    #[Post]
    #[Path('/authorization/callback')]
    #[QueryParam('error', 'string')]
    #[QueryParam('error_description', 'string')]
    #[QueryParam('code', 'string')]
    #[QueryParam('state', 'string')]
    #[Incoming(schema: Passthru::class)]
    #[Outgoing(code: 200, schema: Passthru::class)]
    public function doPost(?string $error, ?string $error_description, ?string $code, ?string $state): mixed
    {
        return $this->execute($error, $error_description, $code, $state);
    }

    private function execute(?string $error, ?string $errorDescription, ?string $code, ?string $state)
    {
        try {
            if (!empty($error)) {
                AuthorizationAbstract::throwErrorException([
                    'error' => $error,
                    'error_description' => $errorDescription ?? 'Unknown error description'
                ]);
            }

            if (empty($code)) {
                throw new RuntimeException('Code not available');
            }

            $accessToken = $this->callback->getAuthorizationCode($code, $state)->getAccessToken(new Grant\AuthorizationCode($code));

            return $this->callback->onAccessToken($accessToken);
        } catch (ErrorExceptionAbstract $e) {
            return $this->callback->onError($e);
        }
    }
}
