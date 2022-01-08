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

use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Http\Exception as StatusCode;
use PSX\Oauth2\Authorization\Exception\ErrorExceptionAbstract;
use PSX\Oauth2\Authorization\Exception\InvalidRequestException;
use PSX\Oauth2\Authorization\Exception\ServerErrorException;
use PSX\Oauth2\Authorization\Exception\UnauthorizedClientException;
use PSX\Oauth2\Authorization\Exception\UnsupportedResponseTypeException;
use PSX\Uri\Url;

/**
 * AuthorizationAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class AuthorizationAbstract extends ControllerAbstract
{
    #[Inject('oauth2_grant_type_factory')]
    protected GrantTypeFactory $grantTypeFactory;

    protected function doGet(HttpContextInterface $context): mixed
    {
        return $this->execute($context);
    }

    protected function doPost(mixed $record, HttpContextInterface $context): mixed
    {
        return $this->execute($context);
    }

    protected function execute(HttpContextInterface $context): mixed
    {
        $responseType = $context->getParameter('response_type');
        $clientId     = $context->getParameter('client_id');
        $redirectUri  = $context->getParameter('redirect_uri');
        $scope        = $context->getParameter('scope');
        $state        = $context->getParameter('state');

        try {
            if (empty($responseType) || empty($clientId) || empty($state)) {
                throw new InvalidRequestException('Missing parameters');
            }

            if (empty($redirectUri)) {
                // in case we have no redirect uri we can get the redirect uri from the app
                $redirectUri = $this->getCallback($clientId);
            } else {
                $redirectUri = new Url($redirectUri);
            }

            $request = new AccessRequest($clientId, $redirectUri, $scope, $state);

            if (!$this->hasGrant($request, $context)) {
                throw new UnauthorizedClientException('Client is not authenticated');
            }

            switch ($responseType) {
                case 'code':
                    $this->handleCode($request, $context);

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

    private function handleCode(AccessRequest $request, HttpContextInterface $context)
    {
        if ($request->hasRedirectUri()) {
            $redirectUri = $request->getRedirectUri();

            $parameters = $redirectUri->getParameters();
            $parameters['code'] = $this->generateCode($request, $context);

            if ($request->hasState()) {
                $parameters['state'] = $request->getState();
            }

            $location = $redirectUri->withParameters($parameters)->toString();

            throw new StatusCode\TemporaryRedirectException($location);
        } else {
            throw new ServerErrorException('No redirect uri available');
        }
    }

    /**
     * This method is called if no redirect_uri was set you can overwrite this
     * method if its possible to get an callback from another source
     */
    protected function getCallback(string $clientId): ?Url
    {
        return null;
    }

    /**
     * Returns whether the user has authorized the client_id. This method must
     * redirect the user to an login form and display an form where the user can
     * grant the authorization request
     */
    abstract protected function hasGrant(AccessRequest $request, HttpContextInterface $context): bool;

    /**
     * Generates an authorization code which is assigned to the request
     */
    abstract protected function generateCode(AccessRequest $request, HttpContextInterface $context): string;
}
