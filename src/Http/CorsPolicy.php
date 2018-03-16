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
        if (!$response->hasHeader('Access-Control-Allow-Origin')) {
            $origin = $request->getHeader('Origin');
            if (!empty($origin)) {
                $header = $this->getAllowedOrigin($origin);
                if ($header !== null) {
                    $response->setHeader('Access-Control-Allow-Origin', $header);
                }
            }
        }

        if (!$response->hasHeader('Access-Control-Allow-Methods')) {
            $method = $request->getHeader('Access-Control-Request-Method');
            if (!empty($method)) {
                $header = $this->getAllowedMethods($method, $allowedMethods);
                if ($header !== null) {
                    $response->setHeader('Access-Control-Allow-Methods', $header);
                }
            }
        }

        if (!$response->hasHeader('Access-Control-Allow-Headers')) {
            $headers = $request->getHeader('Access-Control-Request-Headers');
            if (!empty($headers)) {
                $header = $this->getAllowedHeaders($headers);
                if ($header !== null) {
                    $response->setHeader('Access-Control-Allow-Headers', $header);
                }
            }
        }
    }

    protected function getAllowedOrigin($origin)
    {
        $allowedOrigin = $this->config->get('psx_cors_origin');
        if (is_string($allowedOrigin)) {
            return $allowedOrigin;
        } elseif ($allowedOrigin instanceof \Closure && $allowedOrigin($origin)) {
            return $origin;
        }

        return null;
    }

    protected function getAllowedMethods($method, array $allowedMethods)
    {
        return implode(', ', $allowedMethods);
    }

    protected function getAllowedHeaders($headers)
    {
        $allowed = $this->config->get('psx_cors_headers');
        $headers = array_map('trim', explode(',', $headers));

        $headers = array_uintersect($headers, $allowed, 'strcasecmp');
        if (!empty($headers)) {
            return implode(', ', $headers);
        }

        return null;
    }
}
