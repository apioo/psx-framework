<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Exception as StatusCode;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
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
    /**
     * @Inject("oauth2_grant_type_factory")
     * @var \PSX\Framework\Oauth2\GrantTypeFactory
     */
    protected $grantTypeFactory;

    public function onRequest(RequestInterface $request, ResponseInterface $response)
    {
        if (!in_array($request->getMethod(), ['GET', 'POST'])) {
            throw new StatusCode\MethodNotAllowedException('Invalid request method', ['GET', 'POST']);
        }

        $responseType = $request->getUri()->getParameter('response_type');
        $clientId     = $request->getUri()->getParameter('client_id');
        $redirectUri  = $request->getUri()->getParameter('redirect_uri');
        $scope        = $request->getUri()->getParameter('scope');
        $state        = $request->getUri()->getParameter('state');

        try {
            $request = new AccessRequest($clientId, $redirectUri, $scope, $state);

            if (empty($responseType) || empty($clientId) || empty($state)) {
                throw new InvalidRequestException('Missing parameters');
            }

            if (!$this->hasGrant($request)) {
                throw new UnauthorizedClientException('Client is not authenticated');
            }

            switch ($responseType) {
                case 'code':
                    $this->handleCode($request);
                    break;

                case 'token':
                    $this->handleToken($request);
                    break;

                default:
                    throw new UnsupportedResponseTypeException('Invalid response type');
                    break;
            }
        } catch (ErrorExceptionAbstract $e) {
            if (!empty($redirectUri)) {
                $redirectUri = new Url($redirectUri);

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

    protected function handleCode(AccessRequest $request)
    {
        $redirectUri = $this->getRedirectUri($request);

        if ($redirectUri instanceof Url) {
            $parameters = $redirectUri->getParameters();
            $parameters['code'] = $this->generateCode($request);

            if ($request->hasState()) {
                $parameters['state'] = $request->getState();
            }

            $location = $redirectUri->withParameters($parameters)->toString();

            throw new StatusCode\TemporaryRedirectException($location);
        } else {
            throw new ServerErrorException('No redirect uri available');
        }
    }

    protected function handleToken(AccessRequest $request)
    {
        $redirectUri = $this->getRedirectUri($request);

        if ($redirectUri instanceof Url) {
            // we must create an access token and append it to the redirect_uri
            // fragment or display an redirect form
            $accessToken = $this->grantTypeFactory->get(GrantTypeInterface::TYPE_IMPLICIT)->generateAccessToken(null, array(
                'scope' => $request->getScope()
            ));

            $fields = array(
                'access_token' => $accessToken->getAccessToken(),
                'token_type'   => $accessToken->getTokenType(),
            );

            if ($request->hasState()) {
                $fields['state'] = $request->getState();
            }

            $location = $redirectUri->withFragment(http_build_query($fields, '', '&'))->toString();

            throw new StatusCode\TemporaryRedirectException($location);
        } else {
            throw new ServerErrorException('No redirect uri available');
        }
    }

    protected function getRedirectUri(AccessRequest $request)
    {
        if ($request->hasRedirectUri()) {
            return $request->getRedirectUri();
        } else {
            return $this->getCallback($request->getClientId());
        }
    }

    /**
     * This method is called if no redirect_uri was set you can overwrite this
     * method if its possible to get an callback from another source
     *
     * @param string $clientId
     * @return \PSX\Uri\Url
     */
    protected function getCallback($clientId)
    {
        return null;
    }

    /**
     * Returns whether the user has authorized the client_id. This method must
     * redirect the user to an login form and display an form where the user can
     * grant the authorization request
     *
     * @param \PSX\Framework\Oauth2\AccessRequest $request
     * @return boolean
     */
    abstract protected function hasGrant(AccessRequest $request);

    /**
     * Generates an authorization code which is assigned to the request
     *
     * @param \PSX\Framework\Oauth2\AccessRequest $request
     * @return string
     */
    abstract protected function generateCode(AccessRequest $request);
}
