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

namespace PSX\Framework\Oauth;

use PSX\Data\InvalidDataException;
use PSX\Http\MessageInterface;
use PSX\Oauth\Consumer;
use PSX\Oauth\Data\Request;

/**
 * AuthorizationHeaderExtractor
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AuthorizationHeaderExtractor
{
    /**
     * @var array
     */
    protected $requiredFields;

    /**
     * @var array
     */
    protected $map = array(
        'consumerKey'     => 'consumer_key',
        'token'           => 'token',
        'signatureMethod' => 'signature_method',
        'signature'       => 'signature',
        'timestamp'       => 'timestamp',
        'nonce'           => 'nonce',
        'callback'        => 'callback',
        'version'         => 'version',
        'verifier'        => 'verifier'
    );

    public function __construct(array $requiredFields)
    {
        $this->requiredFields = $requiredFields;
    }

    public function setRequiredFields(array $requiredFields)
    {
        $this->requiredFields = $requiredFields;
    }

    /**
     * @param \PSX\Http\MessageInterface $message
     * @return \PSX\Oauth\Data\Request
     * @throws \PSX\Data\InvalidDataException
     */
    public function extract(MessageInterface $message)
    {
        $request = new Request();
        $auth    = (string) $message->getHeader('Authorization');

        if (!empty($auth)) {
            if (strpos($auth, 'OAuth') !== false) {
                // get oauth data
                $data  = array();
                $items = explode(',', $auth);

                foreach ($items as $v) {
                    $v = trim($v);

                    if (substr($v, 0, 6) == 'oauth_') {
                        $pair = explode('=', $v);

                        if (isset($pair[0]) && isset($pair[1])) {
                            $key = substr(strtolower($pair[0]), 6);
                            $val = trim($pair[1], '"');

                            $data[$key] = Consumer::urlDecode($val);
                        }
                    }
                }

                // check whether all required values are available
                foreach ($this->map as $k => $v) {
                    if (isset($data[$v])) {
                        $method = 'set' . ucfirst($k);

                        if (method_exists($request, $method)) {
                            $request->$method($data[$v]);
                        } else {
                            throw new InvalidDataException('Unknown parameter');
                        }
                    } elseif (in_array($k, $this->requiredFields)) {
                        throw new InvalidDataException('Required parameter "' . $v . '" is missing');
                    }
                }

                return $request;
            } else {
                throw new InvalidDataException('Unknown OAuth authentication');
            }
        } else {
            throw new InvalidDataException('Missing Authorization header');
        }
    }
}
