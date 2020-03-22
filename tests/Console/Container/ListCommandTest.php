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

namespace PSX\Framework\Tests\Console\Container;

use PSX\Framework\Test\Assert;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ContainerCommandTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ListCommandTest extends ControllerTestCase
{
    public function testCommand()
    {
        $command = Environment::getService('console')->find('container:list');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
        ));

        $actual = $commandTester->getDisplay();
        $expect = <<<TEXT

 annotation_reader_factory   PSX\Framework\Annotation\ReaderFactory                     
 api_manager                 PSX\Api\ApiManager                                         
 api_manager                 PSX\Api\ApiManagerInterface                                
 cache                       PSX\Cache\Pool                                             
 cache                       Psr\Cache\CacheItemPoolInterface                           
 config                      PSX\Framework\Config\Config                                
 connection                  Doctrine\DBAL\Connection                                   
 console                     Symfony\Component\Console\Application                      
 container_autowire_resolver PSX\Dependency\AutowireResolver                            
 container_autowire_resolver PSX\Dependency\AutowireResolverInterface                   
 container_inspector         PSX\Dependency\InspectorInterface                          
 container_inspector         PSX\Dependency\Inspector\ContainerInspector                
 container_tag_resolver      PSX\Dependency\TagResolverInterface                        
 container_tag_resolver      PSX\Dependency\TagResolver                                 
 container_type_resolver     PSX\Dependency\TypeResolver                                
 container_type_resolver     PSX\Dependency\TypeResolverInterface                       
 controller_factory          PSX\Framework\Dispatch\ControllerFactory                   
 controller_factory          PSX\Framework\Dispatch\ControllerFactoryInterface          
 cors_policy                 PSX\Framework\Http\CorsPolicy                              
 dispatch                    PSX\Framework\Dispatch\Dispatch                            
 event_dispatcher            Symfony\Component\EventDispatcher\EventDispatcher          
 event_dispatcher            Symfony\Component\EventDispatcher\EventDispatcherInterface 
 exception_converter         PSX\Framework\Exception\ConverterInterface                 
 exception_converter         PSX\Framework\Exception\Converter                          
 generator_factory           PSX\Api\GeneratorFactory                                   
 generator_factory           PSX\Api\GeneratorFactoryInterface                          
 http_client                 PSX\Http\Client\Client                                     
 http_client                 PSX\Http\Client\ClientInterface                            
 io                          PSX\Data\Processor                                         
 listing_filter_factory      PSX\Api\Listing\FilterFactoryInterface                     
 listing_filter_factory      PSX\Api\Listing\FilterFactory                              
 loader                      PSX\Framework\Loader\Loader                                
 loader                      PSX\Framework\Loader\LoaderInterface                       
 loader_location_finder      PSX\Framework\Loader\LocationFinder\RoutingParser          
 loader_location_finder      PSX\Framework\Loader\LocationFinderInterface               
 logger                      Monolog\Logger                                             
 logger                      Psr\Log\LoggerInterface                                    
 object_builder              PSX\Dependency\ObjectBuilderInterface                      
 object_builder              PSX\Dependency\ObjectBuilder                               
 population_service          PSX\Framework\App\Service\Population                       
 request_reader              PSX\Framework\Http\RequestReader                           
 resource_listing            PSX\Framework\Api\ControllerDocumentation                  
 resource_listing            PSX\Api\ListingInterface                                   
 response_writer             PSX\Framework\Http\ResponseWriter                          
 reverse_router              PSX\Framework\Loader\ReverseRouter                         
 routing_parser              PSX\Framework\Loader\RoutingParserInterface                
 routing_parser              PSX\Framework\Loader\RoutingParser\RoutingFile             
 schema_manager              PSX\Schema\SchemaManager                                   
 schema_manager              PSX\Schema\SchemaManagerInterface                          
 session                     PSX\Framework\Session\Session                              
 table_manager               PSX\Sql\TableManagerInterface                              
 table_manager               PSX\Sql\TableManager                                       
 validate                    PSX\Validate\Validate     

TEXT;

        Assert::assertStringMatchIgnoreWhitespace($expect, $actual);
    }
}
