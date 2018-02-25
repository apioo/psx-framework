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

namespace PSX\Framework\Environment\WebServer;

use PSX\Framework\Config\Config;
use PSX\Framework\Dispatch\Dispatch;
use PSX\Framework\Environment\EngineInterface;

/**
 * Uses a classical PHP web server like Apache or Nginx. In this context we dont 
 * need to setup any web server instead the web server calls this code on every 
 * request. We get all request information from the global variables.
 * 
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Engine implements EngineInterface
{
    /**
     * @inheritdoc
     */
    public function serve(Dispatch $dispatch, Config $config)
    {
        $requestFactory  = new RequestFactory($config->get('psx_url'));
        $responseFactory = new ResponseFactory();
        $sender          = new Sender();

        $response = $dispatch->route($requestFactory->createRequest(), $responseFactory->createResponse());

        $sender->send($response);
    }
}
