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

namespace PSX\Framework\Http;

use PSX\Framework\Config\Config;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * CorsPolicy
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CorsPolicy
{
    /**
     * @var \PSX\Framework\Config\Config
     */
    protected $config;

    /**
     * @param \PSX\Framework\Config\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     * @param array $allowedMethods
     */
    public function handle(RequestInterface $request, ResponseInterface $response, array $allowedMethods)
    {
        $allowedOrigin  = $this->config->get('psx_cors_origin');
        $allowedHeaders = $this->config->get('psx_cors_headers');

        $allow  = false;
        $origin = $request->getHeader('Origin');
        if (!empty($origin)) {
            if (is_string($allowedOrigin)) {
                $response->setHeader('Access-Control-Allow-Origin', $allowedOrigin);
                $allow = true;
            } elseif ($allowedOrigin instanceof \Closure) {
                if ($allowedOrigin($origin)) {
                    $response->setHeader('Access-Control-Allow-Origin', $origin);
                    $response->addHeader('Vary', 'Origin');
                    $allow = true;
                }
            }
        }

        if ($allow && $request->getMethod() == 'OPTIONS') {
            $method = $request->getHeader('Access-Control-Request-Method');
            if (!empty($method)) {
                $response->setHeader('Access-Control-Allow-Methods', implode(', ', $allowedMethods));
            }

            $headers = $request->getHeader('Access-Control-Request-Headers');
            if (!empty($headers)) {
                $response->setHeader('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));
            }
        }
    }
}
