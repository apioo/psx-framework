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

use PSX\Api\Attribute\HeaderParam;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Post;
use PSX\Api\Model\Passthru;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Http\ResponseWriter;
use PSX\Framework\OAuth2\Credentials;
use PSX\Framework\OAuth2\GrantTypeFactory;
use PSX\Http\FilterChainInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\OAuth2\AccessToken;
use PSX\OAuth2\Exception\ErrorExceptionAbstract;
use PSX\OAuth2\Error;
use PSX\OAuth2\GrantFactory;
use PSX\Schema\Type;

/**
 * OAuth2 token endpoint controller
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 * @see     https://www.rfc-editor.org/rfc/rfc6749
 */
class TokenController extends ControllerAbstract
{
    private GrantTypeFactory $grantTypeFactory;
    private ResponseWriter $responseWriter;

    public function __construct(GrantTypeFactory $grantTypeFactory, ResponseWriter $responseWriter)
    {
        $this->grantTypeFactory = $grantTypeFactory;
        $this->responseWriter = $responseWriter;
    }

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

    #[Post]
    #[Path('/authorization/token')]
    #[HeaderParam('authorization', Type::STRING)]
    #[Incoming(schema: Passthru::class)]
    public function doPost(?string $authorization, \stdClass $payload): AccessToken
    {
        $grant = GrantFactory::factory((array) $payload);
        $parts = explode(' ', $authorization ?? '', 2);
        $type  = $parts[0] ?? null;
        $data  = $parts[1] ?? null;

        $credentials = null;
        if ($type == 'Basic' && !empty($data)) {
            $data = explode(':', base64_decode($data), 2);
            $clientId = $data[0] ?? null;
            $clientSecret = $data[1] ?? null;

            if (!empty($clientId) && !empty($clientSecret)) {
                $credentials = new Credentials($clientId, $clientSecret);
            }
        }

        return $this->grantTypeFactory->get($grant->getGrantType())->generateAccessToken($credentials, $grant);
    }
}
