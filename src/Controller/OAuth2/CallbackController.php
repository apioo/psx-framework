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

namespace PSX\Framework\Controller\OAuth2;

use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\OperationId;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Post;
use PSX\Api\Attribute\QueryParam;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\OAuth2\CallbackInterface;
use PSX\OAuth2\AuthorizationAbstract;
use PSX\OAuth2\Exception\ErrorExceptionAbstract;
use PSX\OAuth2\Grant;
use PSX\Schema\Type;
use RuntimeException;

/**
 * CallbackController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
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
    #[QueryParam('error', Type::STRING)]
    #[QueryParam('error_description', Type::STRING)]
    #[QueryParam('code', Type::STRING)]
    #[QueryParam('state', Type::STRING)]
    #[OperationId('oauth.callback')]
    public function callbackGet(?string $error, ?string $error_description, ?string $code, ?string $state): mixed
    {
        return $this->execute($error, $error_description, $code, $state);
    }

    #[Post]
    #[Path('/authorization/callback')]
    #[QueryParam('error', Type::STRING)]
    #[QueryParam('error_description', Type::STRING)]
    #[QueryParam('code', Type::STRING)]
    #[QueryParam('state', Type::STRING)]
    #[OperationId('oauth.callback')]
    public function callbackPost(?string $error, ?string $error_description, ?string $code, ?string $state): mixed
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
