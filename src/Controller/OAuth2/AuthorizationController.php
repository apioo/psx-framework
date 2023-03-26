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
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Post;
use PSX\Api\Attribute\QueryParam;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\OAuth2\AccessRequest;
use PSX\Framework\OAuth2\AuthorizerInterface;
use PSX\Framework\OAuth2\GrantTypeFactory;
use PSX\Http\Exception as StatusCode;
use PSX\OAuth2\Exception\ErrorExceptionAbstract;
use PSX\OAuth2\Exception\InvalidRequestException;
use PSX\OAuth2\Exception\ServerErrorException;
use PSX\OAuth2\Exception\UnauthorizedClientException;
use PSX\OAuth2\Exception\UnsupportedResponseTypeException;
use PSX\Uri\Url;

/**
 * AuthorizationController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class AuthorizationController extends ControllerAbstract
{
    private GrantTypeFactory $grantTypeFactory;
    private AuthorizerInterface $authorizer;

    public function __construct(GrantTypeFactory $grantTypeFactory, AuthorizerInterface $authorizer)
    {
        $this->grantTypeFactory = $grantTypeFactory;
        $this->authorizer = $authorizer;
    }

    #[Get]
    #[Path('/authorization/authorize')]
    #[QueryParam('response_type', 'string')]
    #[QueryParam('client_id', 'string')]
    #[QueryParam('redirect_uri', 'string')]
    #[QueryParam('scope', 'string')]
    #[QueryParam('state', 'string')]
    public function doGet(?string $response_type, ?string $client_id, ?string $redirect_uri, ?string $scope, ?string $state): mixed
    {
        return $this->execute($response_type, $client_id, $redirect_uri, $scope, $state);
    }

    #[Post]
    #[Path('/authorization/authorize')]
    #[QueryParam('response_type', 'string')]
    #[QueryParam('client_id', 'string')]
    #[QueryParam('redirect_uri', 'string')]
    #[QueryParam('scope', 'string')]
    #[QueryParam('state', 'string')]
    public function doPost(?string $response_type, ?string $client_id, ?string $redirect_uri, ?string $scope, ?string $state): mixed
    {
        return $this->execute($response_type, $client_id, $redirect_uri, $scope, $state);
    }

    protected function execute(?string $responseType, ?string $clientId, ?string $redirectUri, ?string $scope, ?string $state): mixed
    {
        try {
            if (empty($responseType) || empty($clientId) || empty($state)) {
                throw new InvalidRequestException('Missing parameters');
            }

            if (empty($redirectUri)) {
                // in case we have no redirect uri we can get the redirect uri from the app
                $redirectUri = $this->authorizer->getCallback($clientId);
            } else {
                $redirectUri = Url::parse($redirectUri);
            }

            $request = new AccessRequest($clientId, $redirectUri, $scope, $state);

            if (!$this->authorizer->hasGrant($request)) {
                throw new UnauthorizedClientException('Client is not authenticated');
            }

            switch ($responseType) {
                case 'code':
                    $this->handleCode($request);

                default:
                    throw new UnsupportedResponseTypeException('Invalid response type');
            }
        } catch (ErrorExceptionAbstract $e) {
            if ($redirectUri instanceof Url) {
                $parameters = $redirectUri->getParameters();
                $parameters['error'] = $e->getType();
                $parameters['error_description'] = $e->getMessage();

                $location = $redirectUri->withParameters($parameters)->toString();

                throw new StatusCode\TemporaryRedirectException($location);
            } else {
                throw $e;
            }
        }
    }

    private function handleCode(AccessRequest $request)
    {
        if ($request->hasRedirectUri()) {
            $redirectUri = $request->getRedirectUri();

            $parameters = $redirectUri->getParameters();
            $parameters['code'] = $this->authorizer->generateCode($request);

            if ($request->hasState()) {
                $parameters['state'] = $request->getState();
            }

            $location = $redirectUri->withParameters($parameters)->toString();

            throw new StatusCode\TemporaryRedirectException($location);
        } else {
            throw new ServerErrorException('No redirect uri available');
        }
    }
}
