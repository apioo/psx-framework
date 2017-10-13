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

namespace PSX\Framework\Oauth2\Schema;

use PSX\Schema\Property;
use PSX\Schema\SchemaAbstract;

/**
 * Request
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Request extends SchemaAbstract
{
    public function getDefinition()
    {
        $sb = $this->getSchemaBuilder('authorization_code');
        $sb->string('grant_type')->setConst('authorization_code');
        $sb->string('code');
        $sb->string('redirect_uri');
        $sb->string('client_id');
        $sb->setRequired(['grant_type', 'code']);
        $authorizationCode = $sb->getProperty();

        $sb = $this->getSchemaBuilder('password');
        $sb->string('grant_type')->setConst('password');
        $sb->string('username');
        $sb->string('password');
        $sb->string('scope');
        $sb->setRequired(['grant_type', 'username', 'password']);
        $password = $sb->getProperty();

        $sb = $this->getSchemaBuilder('client_credentials');
        $sb->string('grant_type')->setConst('client_credentials');
        $sb->string('scope');
        $sb->setRequired(['grant_type']);
        $clientCredentials = $sb->getProperty();

        $sb = $this->getSchemaBuilder('refresh_token');
        $sb->string('grant_type')->setConst('refresh_token');
        $sb->string('refresh_token');
        $sb->string('scope');
        $sb->setRequired(['grant_type', 'refresh_token']);
        $refreshToken = $sb->getProperty();

        return Property::get()
            ->setTitle('authorization')
            ->setOneOf([
                $authorizationCode,
                $password,
                $clientCredentials,
                $refreshToken,
            ]);
    }
}
