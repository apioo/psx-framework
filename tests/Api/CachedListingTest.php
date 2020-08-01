<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Tests\Api;

use PSX\Api\SpecificationInterface;
use PSX\Cache\Pool;
use PSX\Api\Listing\CachedListing;
use PSX\Framework\Test\ControllerDbTestCase;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiController;

/**
 * CachedListingTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CachedListingTest extends ControllerDbTestCase
{
    public function getDataSet()
    {
        return $this->createFromFile(__DIR__ . '/../table_fixture.php');
    }

    public function testGetResourceIndex()
    {
        $listing   = new CachedListing(Environment::getService('resource_listing'), Environment::getService('cache'));
        $resources = $listing->getAvailableRoutes();

        $this->assertEquals(2, count($resources));

        $this->assertEquals(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], $resources[0]->getMethods());
        $this->assertEquals('/bar', $resources[0]->getPath());

        $this->assertEquals(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], $resources[1]->getMethods());
        $this->assertEquals('/foo', $resources[1]->getPath());
    }

    public function testGetDocumentation()
    {
        $listing = new CachedListing(Environment::getService('resource_listing'), Environment::getService('cache'));
        $specification = $listing->find('/foo');

        $this->assertInstanceOf(SpecificationInterface::class, $specification);

        $resource = $specification->getResourceCollection()->get('/foo');

        $this->assertEquals(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], $resource->getAllowedMethods());

        $this->assertEmpty($resource->getMethod('GET')->getRequest());
        $this->assertEquals('Collection', $resource->getMethod('GET')->getResponse(200));
        $this->assertEquals('Create', $resource->getMethod('POST')->getRequest());
        $this->assertEquals('Message', $resource->getMethod('POST')->getResponse(201));
        $this->assertEquals('Update', $resource->getMethod('PUT')->getRequest());
        $this->assertEquals('Message', $resource->getMethod('PUT')->getResponse(200));
        $this->assertEquals('Delete', $resource->getMethod('DELETE')->getRequest());
        $this->assertEquals('Message', $resource->getMethod('DELETE')->getResponse(200));
        $this->assertEquals('Patch', $resource->getMethod('PATCH')->getRequest());
        $this->assertEquals('Message', $resource->getMethod('PATCH')->getResponse(200));
    }

    public function testInvalidateResource()
    {
        $cache = $this->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->setMethods(['deleteItem'])
            ->getMock();

        $cache->expects($this->once())
            ->method('deleteItem')
            ->with($this->equalTo('api-resource-1effb2475fcfba4f-0'));

        $listing = new CachedListing(Environment::getService('resource_listing'), $cache);
        $listing->invalidateResource('/foo');
    }

    protected function getPaths()
    {
        return array(
            [['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/bar', TestSchemaApiController::class],
            [['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/foo', TestSchemaApiController::class],
        );
    }
}
