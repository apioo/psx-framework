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

use PSX\Api\Resource;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Http\FilterChainInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Oauth2\Authorization\Exception\ErrorExceptionAbstract;

/**
 * TokenAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class TokenAbstract extends SchemaApiAbstract
{
    /**
     * @Inject("oauth2_grant_type_factory")
     * @var \PSX\Framework\Oauth2\GrantTypeFactory
     */
    protected $grantTypeFactory;

    /**
     * @param integer $version
     * @return \PSX\Api\Resource
     */
    public function getDocumentation($version = null)
    {
        $resource = new Resource(Resource::STATUS_ACTIVE, $this->context->getPath());

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($this->schemaManager->getSchema(Schema\Request::class))
            ->addResponse(200, $this->schemaManager->getSchema(Schema\AccessToken::class))
            ->addResponse(400, $this->schemaManager->getSchema(Schema\Error::class))
        );

        return $resource;
    }

    public function getPreFilter()
    {
        return [function(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain){
            try {
                // the endpoint must return a specific error response see:
                // https://tools.ietf.org/html/rfc6749#section-5.2
                $filterChain->handle($request, $response);
            } catch (ErrorExceptionAbstract $e) {
                $error = new Error();
                $error->setError($e->getType());
                $error->setErrorDescription($e->getMessage());
                $error->setState(null);

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
                $error = new Error();
                $error->setError('server_error');
                $error->setErrorDescription($e->getMessage());
                $error->setState(null);

                $response->setStatus(400);
                $this->responseWriter->setBody($response, $error);
            }
        }];
    }

    public function doPost($record, HttpContextInterface $context)
    {
        $parameters  = $record->getProperties();
        $grantType   = isset($parameters['grant_type']) ? $parameters['grant_type'] : null;
        $scope       = isset($parameters['scope']) ? $parameters['scope'] : null;
        $credentials = null;

        $auth  = $context->getHeader('Authorization');
        $parts = explode(' ', $auth, 2);
        $type  = isset($parts[0]) ? $parts[0] : null;
        $data  = isset($parts[1]) ? $parts[1] : null;

        if ($type == 'Basic' && !empty($data)) {
            $data         = explode(':', base64_decode($data), 2);
            $clientId     = isset($data[0]) ? $data[0] : null;
            $clientSecret = isset($data[1]) ? $data[1] : null;

            if (!empty($clientId) && !empty($clientSecret)) {
                $credentials = new Credentials($clientId, $clientSecret);
            }
        }

        if ($credentials === null && isset($parameters['client_id']) && isset($parameters['client_secret'])) {
            $credentials = new Credentials($parameters['client_id'], $parameters['client_secret']);
        }

        // we get the grant type factory from the DI container the factory
        // contains the available grant types
        return $this->grantTypeFactory->get($grantType)->generateAccessToken($credentials, $parameters);
    }
}
