<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Controller\Tool;

use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Path;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Loader\ReverseRouter;
use PSX\Framework\Model\DiscoveryCollection;
use PSX\Framework\Model\DiscoveryLink;

/**
 * DiscoveryController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DiscoveryController extends ControllerAbstract
{
    private ReverseRouter $reverseRouter;

    public function __construct(ReverseRouter $reverseRouter)
    {
        $this->reverseRouter = $reverseRouter;
    }

    #[Path('/system/discovery')]
    #[Get]
    public function show(): DiscoveryCollection
    {
        $collection = new DiscoveryCollection();
        $collection->setLinks($this->getLinks());
        return $collection;
    }

    private function getLinks(): array
    {
        $links = [];

        $apiPath = $this->reverseRouter->getDispatchUrl();
        if ($apiPath !== null) {
            $links[] = $this->newLink('api', $apiPath);
        }

        $routingPath = $this->reverseRouter->getUrl(RoutingController::class);
        if ($routingPath !== null) {
            $links[] = $this->newLink('routing', $routingPath);
        }

        $generatorPath = $this->reverseRouter->getUrl(GeneratorController::class);
        if ($generatorPath !== null) {
            $links[] = $this->newLink('generator', $generatorPath);
        }

        return $links;
    }

    private function newLink(string $rel, string $href): DiscoveryLink
    {
        $link = new DiscoveryLink();
        $link->setRel($rel);
        $link->setHref($href);
        return $link;
    }
}
