<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Environment;

use PSX\Framework\Config\ConfigInterface;
use PSX\Http\RequestInterface;

/**
 * IPResolver
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class IPResolver
{
    public function __construct(private ConfigInterface $config)
    {
    }

    public function resolveByEnvironment(): string
    {
        $trustedIpHeader = $this->config->get('psx_trusted_ip_header');
        if (!empty($trustedIpHeader)) {
            $key = 'HTTP_' . strtoupper(str_replace('-', '_', $trustedIpHeader));
            if (isset($_SERVER[$key]) && is_string($_SERVER[$key])) {
                return $_SERVER[$key];
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function resolveByRequest(RequestInterface $request): string
    {
        $trustedIpHeader = $this->config->get('psx_trusted_ip_header');
        if (!empty($trustedIpHeader)) {
            $ip = $request->getHeader($trustedIpHeader);
            if (!empty($ip)) {
                return $ip;
            }
        }

        return $request->getAttribute('REMOTE_ADDR') ?? '127.0.0.1';
    }
}
