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

use PSX\Oauth2\Error;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Schema\Passthru;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Http\FilterChainInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Oauth2\AccessToken;
use PSX\Oauth2\Authorization\Exception\ErrorExceptionAbstract;
use PSX\Oauth2\GrantFactory;

/**
 * TokenAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class TokenAbstract extends ControllerAbstract
{
    #[Inject('oauth2_grant_type_factory')]
    protected GrantTypeFactory $grantTypeFactory;

    public function getPreFilter(): array
    {
        $filter = parent::getPreFilter();

        $filter[] = function(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain): void {
            try {
                // the endpoint must return a specific error response see:
                // https://tools.ietf.org/html/rfc6749#section-5.2
                $filterChain->handle($request, $response);
            } catch (ErrorExceptionAbstract $e) {
                $error = new Error($e->getType(), $e->getMessage());

                if ($e->getType() == 'invalid_client') {
                    $response->setStatus(401);

                    if ($request->hasHeader('Authorization')) {
                        $response->setHeader('WWW-Authenticate', 'Bearer');
                    }
                } else {
                    $response->setStatus(400);
                }

                $this->responseWriter->setBody($response, $error);
            } catch (\Throwable $e) {
                $error = new Error('server_error', $e->getMessage());

                $response->setStatus(400);
                $this->responseWriter->setBody($response, $error);
            }
        };

        return $filter;
    }

    #[Incoming(schema: Passthru::class)]
    #[Outgoing(code: 200, schema: AccessToken::class)]
    #[Outgoing(code: 400, schema: Error::class)]
    public function doPost(mixed $record, HttpContextInterface $context): AccessToken
    {
        $grant       = GrantFactory::factory((array) $record);
        $credentials = null;

        $auth  = $context->getHeader('Authorization');
        $parts = explode(' ', $auth, 2);
        $type  = $parts[0] ?? null;
        $data  = $parts[1] ?? null;

        if ($type == 'Basic' && !empty($data)) {
            $data = explode(':', base64_decode($data), 2);
            $clientId = $data[0] ?? null;
            $clientSecret = $data[1] ?? null;

            if (!empty($clientId) && !empty($clientSecret)) {
                $credentials = new Credentials($clientId, $clientSecret);
            }
        }

        $parameters = $context->getParameters();
        if ($credentials === null && isset($parameters['client_id']) && isset($parameters['client_secret'])) {
            $credentials = new Credentials($parameters['client_id'], $parameters['client_secret']);
        }

        return $this->grantTypeFactory->get($grant->getGrantType())->generateAccessToken($credentials, $grant);
    }
}
