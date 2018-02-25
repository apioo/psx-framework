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

namespace PSX\Framework\Tests\Oauth2;

use PSX\Framework\Oauth2\AccessRequest;
use PSX\Framework\Oauth2\AuthorizationAbstract;

/**
 * TestAuthorizationAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestAuthorizationAbstract extends AuthorizationAbstract
{
    protected function hasGrant(AccessRequest $request)
    {
        // normally we must check whether the user is authenticated and if not
        // we must redirect them to an login form which redirects the user back
        // if the login was successful. In this case we use the get parameter
        // for testing purpose

        return $this->getParameter('has_grant');
    }

    protected function generateCode(AccessRequest $request)
    {
        // this code must be stored in an database so we can later check whether
        // the code was generated. In this case we use the get parameter for
        // testing purpose

        return $this->getParameter('code');
    }
}
