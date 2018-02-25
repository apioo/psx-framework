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

namespace PSX\Framework\Tests\Oauth;

use PSX\Framework\Oauth\RequestAbstract;
use PSX\Oauth\Data\Credentials;
use PSX\Oauth\Data\Request;
use PSX\Oauth\Data\Response;

/**
 * TestRequestAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestRequestAbstract extends RequestAbstract
{
    protected function getConsumer($consumerKey)
    {
        return new Credentials(FlowTest::CONSUMER_KEY, FlowTest::CONSUMER_SECRET);
    }

    protected function getResponse(Credentials $consumer, Request $request)
    {
        $response = new Response();
        $response->setToken(FlowTest::TMP_TOKEN);
        $response->setTokenSecret(FlowTest::TMP_TOKEN_SECRET);

        return $response;
    }
}
