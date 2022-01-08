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

namespace PSX\Framework\Oauth2\AuthorizationCode;

use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Oauth2\AccessToken;
use PSX\Oauth2\Grant;
use PSX\Oauth2\Authorization\AuthorizationCode;
use PSX\Oauth2\Authorization\Exception\ErrorExceptionAbstract;
use PSX\Oauth2\AuthorizationAbstract;
use RuntimeException;

/**
 * CallbackAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class CallbackAbstract extends ControllerAbstract
{
    public function doGet(HttpContextInterface $context): mixed
    {
        return $this->execute($context);
    }

    public function doPost(mixed $record, HttpContextInterface $context): mixed
    {
        return $this->execute($context);
    }

    private function execute(HttpContextInterface $context)
    {
        try {
            $error = $context->getParameter('error');

            if (!empty($error)) {
                AuthorizationAbstract::throwErrorException($context->getParameters());
            }

            $code  = $context->getParameter('code');
            $state = $context->getParameter('state');

            if (empty($code)) {
                throw new RuntimeException('Code not available');
            }

            $accessToken = $this->getAuthorizationCode($code, $state)->getAccessToken(new Grant\AuthorizationCode($code));

            return $this->onAccessToken($accessToken, $context);
        } catch (ErrorExceptionAbstract $e) {
            return $this->onError($e, $context);
        }
    }

    /**
     * Should return the authorization code object containing the endpoint url and the client id and secret
     */
    abstract protected function getAuthorizationCode(string $code, string $state): AuthorizationCode;

    /**
     * Is called if we have obtained an access token from the authorization server
     */
    abstract protected function onAccessToken(AccessToken $accessToken, HttpContextInterface $context): mixed;

    /**
     * Is called if the client was redirected with an GET error parameter
     */
    abstract protected function onError(\Throwable $e, HttpContextInterface $context): mixed;
}
