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

namespace PSX\Framework\Tests\Controller\Foo\Application\SchemaApi;

use PSX\Api\Resource;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Tests\Controller\Foo\Schema;
use PSX\Schema\Property;

/**
 * PropertyDocumentationController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PropertyDocumentationController extends SchemaApiAbstract
{
    use PropertyControllerTrait;

    /**
     * @Inject
     * @var \PSX\Schema\SchemaManager
     */
    protected $schemaManager;

    public function getDocumentation($version = null)
    {
        $resource = new Resource(Resource::STATUS_ACTIVE, $this->context->getPath());

        $resource->addPathParameter('id', Property::getInteger());

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->addQueryParameter('type', Property::getInteger())
            ->addResponse(200, $this->schemaManager->getSchema(Schema\Property::class))
        );

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($this->schemaManager->getSchema(Schema\Property::class))
            ->addResponse(200, $this->schemaManager->getSchema(Schema\Property::class))
        );

        return $resource;
    }
}
