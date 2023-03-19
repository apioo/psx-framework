<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Framework\OAuth2\Schema;

use PSX\Schema\Property;
use PSX\Schema\SchemaAbstract;
use PSX\Schema\TypeFactory;

/**
 * Request
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Request extends SchemaAbstract
{
    public function build(): void
    {
        $type = $this->newStruct('OAuth2_Authorization_Code');
        $type->addString('grant_type')->setConst('authorization_code');
        $type->addString('code');
        $type->addString('redirect_uri');
        $type->addString('client_id');
        $type->setRequired(['grant_type', 'code']);

        $type = $this->newStruct('OAuth2_Password');
        $type->addString('grant_type')->setConst('password');
        $type->addString('username');
        $type->addString('password');
        $type->addString('scope');
        $type->setRequired(['grant_type', 'username', 'password']);

        $type = $this->newStruct('OAuth2_Client_Credentials');
        $type->addString('grant_type')->setConst('client_credentials');
        $type->addString('scope');
        $type->setRequired(['grant_type']);

        $type = $this->newStruct('OAuth2_Refresh_Token');
        $type->addString('grant_type')->setConst('refresh_token');
        $type->addString('refresh_token');
        $type->addString('scope');
        $type->setRequired(['grant_type', 'refresh_token']);

        $this->add('OAuth2_Request', TypeFactory::getUnion([
            TypeFactory::getReference('OAuth2_Authorization_Code'),
            TypeFactory::getReference('OAuth2_Password'),
            TypeFactory::getReference('OAuth2_Client_Credentials'),
            TypeFactory::getReference('OAuth2_Refresh_Token'),
        ]));

        $this->setRoot('OAuth2_Request');
    }
}
