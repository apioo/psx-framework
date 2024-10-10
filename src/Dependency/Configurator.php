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

namespace PSX\Framework\Dependency;

use PSX\Api\ConfiguratorInterface;
use PSX\Api\Repository\RepositoryInterface;
use PSX\Framework\Controller\ControllerInterface;
use PSX\Framework\OAuth2\GrantTypeInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;

/**
 * Configurator
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org
 */
class Configurator
{
    public static function services(ServicesConfigurator $services): ServicesConfigurator
    {
        $services->defaults()
            ->autowire()
            ->autoconfigure();

        $services
            ->instanceof(ControllerInterface::class)
            ->tag('psx.controller');

        $services
            ->instanceof(Command::class)
            ->tag('psx.command');

        $services
            ->instanceof(EventSubscriberInterface::class)
            ->tag('psx.event_subscriber');

        $services
            ->instanceof(GrantTypeInterface::class)
            ->tag('psx.oauth2_grant');

        $services
            ->instanceof(RepositoryInterface::class)
            ->tag('psx.api_repository');

        $services
            ->instanceof(ConfiguratorInterface::class)
            ->tag('psx.api_configurator');

        $services
            ->instanceof(MiddlewareInterface::class)
            ->tag('psx.messenger_middleware');

        $services
            ->instanceof(TransportFactoryInterface::class)
            ->tag('psx.messenger_transport_factory');

        return $services;
    }
}
