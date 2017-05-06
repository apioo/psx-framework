<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Tests\Data\Writer;

use PSX\Record\Record;
use PSX\Data\Tests\WriterTestCase;
use PSX\DateTime\DateTime;
use PSX\Framework\Loader\ReverseRouter;
use PSX\Framework\Template\TemplateInterface;

/**
 * TemplateAbstractTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class TemplateAbstractTestCase extends \PHPUnit_Framework_TestCase
{
    protected $basePath;

    protected function setUp()
    {
        $this->basePath = str_replace('\\', '/', PSX_PATH_LIBRARY);
    }

    /**
     * Returns the writer
     *
     * @param \PSX\Framework\Template\TemplateInterface $template
     * @param \PSX\Framework\Loader\ReverseRouter $router
     * @return \PSX\Data\WriterInterface
     */
    abstract protected function getWriter(TemplateInterface $template, ReverseRouter $router);

    public function testWrite()
    {
        $template = $this->createMock('PSX\Framework\Template\TemplateInterface');
        $router   = $this->getMockBuilder('PSX\Framework\Loader\ReverseRouter')
            ->disableOriginalConstructor()
            ->getMock();

        $template->expects($this->at(3))
                ->method('isFileAvailable')
                ->will($this->returnValue(true));

        $template->expects($this->at(4))
                ->method('assign')
                ->with($this->equalTo('self'));

        $template->expects($this->at(5))
                ->method('assign')
                ->with($this->equalTo('url'));

        $template->expects($this->at(6))
                ->method('assign')
                ->with($this->equalTo('base'));

        $template->expects($this->at(7))
                ->method('assign')
                ->with($this->equalTo('render'));

        $template->expects($this->at(8))
                ->method('assign')
                ->with($this->equalTo('location'));

        $template->expects($this->at(9))
                ->method('assign')
                ->with($this->equalTo('router'), $this->identicalTo($router));

        $template->expects($this->at(10))
                ->method('assign')
                ->with($this->equalTo('controllerClass'));

        $template->expects($this->at(11))
                ->method('assign')
                ->with($this->equalTo('id'), $this->equalTo(1));

        $template->expects($this->at(12))
                ->method('assign')
                ->with($this->equalTo('author'), $this->equalTo('foo'));

        $template->expects($this->at(13))
                ->method('assign')
                ->with($this->equalTo('title'), $this->equalTo('bar'));

        $template->expects($this->at(14))
                ->method('assign')
                ->with($this->equalTo('content'), $this->equalTo('foobar'));

        $template->expects($this->at(15))
                ->method('assign')
                ->with($this->equalTo('date'));

        $template->expects($this->at(16))
                ->method('transform')
                ->will($this->returnValue('foo'));

        $writer = $this->getWriter($template, $router);
        $actual = $writer->write($this->getRecord());

        $expect = <<<TEXT
foo
TEXT;

        $this->assertEquals($expect, $actual);
    }

    public function testWriteResultSet()
    {
        $template = $this->createMock('PSX\Framework\Template\TemplateInterface');
        $router   = $this->getMockBuilder('PSX\Framework\Loader\ReverseRouter')
            ->disableOriginalConstructor()
            ->getMock();

        $template->expects($this->at(3))
                ->method('isFileAvailable')
                ->will($this->returnValue(true));

        $template->expects($this->at(4))
                ->method('assign')
                ->with($this->equalTo('self'));

        $template->expects($this->at(5))
                ->method('assign')
                ->with($this->equalTo('url'));

        $template->expects($this->at(6))
                ->method('assign')
                ->with($this->equalTo('base'));

        $template->expects($this->at(7))
                ->method('assign')
                ->with($this->equalTo('render'));

        $template->expects($this->at(8))
                ->method('assign')
                ->with($this->equalTo('location'));

        $template->expects($this->at(9))
                ->method('assign')
                ->with($this->equalTo('router'), $this->identicalTo($router));

        $template->expects($this->at(10))
                ->method('assign')
                ->with($this->equalTo('controllerClass'));

        $template->expects($this->at(11))
                ->method('assign')
                ->with($this->equalTo('totalResults'), $this->equalTo(2));

        $template->expects($this->at(12))
                ->method('assign')
                ->with($this->equalTo('startIndex'), $this->equalTo(0));

        $template->expects($this->at(13))
                ->method('assign')
                ->with($this->equalTo('itemsPerPage'), $this->equalTo(8));

        $template->expects($this->at(14))
                ->method('assign')
                ->with($this->equalTo('entry'));

        $template->expects($this->at(15))
                ->method('transform')
                ->will($this->returnValue('foo'));

        $writer = $this->getWriter($template, $router);
        $actual = $writer->write($this->getResultSet());

        $expect = <<<TEXT
foo
TEXT;

        $this->assertEquals($expect, $actual);
    }

    /**
     * When no template was set we get the template from the controller class
     * name
     */
    public function testAutomaticTemplateDetection()
    {
        $template = $this->createMock('PSX\Framework\Template\TemplateInterface');
        $router   = $this->getMockBuilder('PSX\Framework\Loader\ReverseRouter')
            ->disableOriginalConstructor()
            ->getMock();

        $template->expects($this->at(0))
                ->method('hasFile')
                ->will($this->returnValue(false));

        $template->expects($this->at(1))
                ->method('setDir')
                ->with($this->equalTo($this->basePath . '/Foo/Resource'));

        $template->expects($this->at(3))
                ->method('isFileAvailable')
                ->will($this->returnValue(true));

        $writer = $this->getWriter($template, $router);
        $writer->setControllerFile($this->basePath . '/Foo/Application/News/DetailDescription.php');

        $template->expects($this->at(2))
                ->method('set')
                ->with($this->equalTo('news/detail_description.' . $writer->getFileExtension()));

        $actual = $writer->write($this->getRecord());
    }

    /**
     * If a template file was set but the file doesnt actually exist we use the
     * fitting dir from the controller class name
     */
    public function testSetNotExistingTemplateFile()
    {
        $template = $this->createMock('PSX\Framework\Template\TemplateInterface');
        $router   = $this->getMockBuilder('PSX\Framework\Loader\ReverseRouter')
            ->disableOriginalConstructor()
            ->getMock();

        $template->expects($this->at(0))
                ->method('hasFile')
                ->will($this->returnValue(true));

        $template->expects($this->at(1))
                ->method('isAbsoluteFile')
                ->will($this->returnValue(false));

        $template->expects($this->at(2))
                ->method('setDir')
                ->with($this->equalTo($this->basePath . '/Foo/Resource'));

        $template->expects($this->at(3))
                ->method('isFileAvailable')
                ->will($this->returnValue(true));

        $writer = $this->getWriter($template, $router);
        $writer->setControllerFile($this->basePath . '/Foo/Application/News/DetailDescription.php');

        $actual = $writer->write($this->getRecord());
    }

    /**
     * If a template file was set which exists we simply use this file and dont
     * set any dir. The template file must have an file extension which is
     * supported by the writer
     */
    public function testSetExistingTemplateFile()
    {
        $template = $this->createMock('PSX\Framework\Template\TemplateInterface');
        $router   = $this->getMockBuilder('PSX\Framework\Loader\ReverseRouter')
            ->disableOriginalConstructor()
            ->getMock();

        $writer = $this->getWriter($template, $router);
        $writer->setControllerFile($this->basePath . '/Foo/Application/News/DetailDescription.php');

        $template->expects($this->at(0))
                ->method('hasFile')
                ->will($this->returnValue(true));

        $template->expects($this->at(1))
                ->method('isAbsoluteFile')
                ->will($this->returnValue(true));

        $template->expects($this->at(2))
                ->method('get')
                ->will($this->returnValue('/foo/template.' . $writer->getFileExtension()));

        $template->expects($this->at(3))
                ->method('setDir')
                ->with($this->equalTo(null));

        $template->expects($this->at(4))
                ->method('isFileAvailable')
                ->will($this->returnValue(true));

        $actual = $writer->write($this->getRecord());
    }

    public function testSetExistingTemplateFileWrongFileExtension()
    {
        $template = $this->createMock('PSX\Framework\Template\TemplateInterface');
        $router   = $this->getMockBuilder('PSX\Framework\Loader\ReverseRouter')
            ->disableOriginalConstructor()
            ->getMock();

        $writer = $this->getWriter($template, $router);
        $writer->setControllerFile($this->basePath . '/Foo/Application/News/DetailDescription.php');

        $template->expects($this->at(0))
                ->method('hasFile')
                ->will($this->returnValue(true));

        $template->expects($this->at(1))
                ->method('isAbsoluteFile')
                ->will($this->returnValue(true));

        $template->expects($this->at(2))
                ->method('get')
                ->will($this->returnValue('/foo/template.foo'));

        $template->expects($this->at(3))
                ->method('set')
                ->with($this->equalTo('/foo/template.' . $writer->getFileExtension()));

        $template->expects($this->at(4))
                ->method('setDir')
                ->with($this->equalTo(null));

        $template->expects($this->at(5))
                ->method('isFileAvailable')
                ->will($this->returnValue(true));

        $actual = $writer->write($this->getRecord());
    }

    public function testSetExistingTemplateFileNoFileExtension()
    {
        $template = $this->createMock('PSX\Framework\Template\TemplateInterface');
        $router   = $this->getMockBuilder('PSX\Framework\Loader\ReverseRouter')
            ->disableOriginalConstructor()
            ->getMock();

        $writer = $this->getWriter($template, $router);
        $writer->setControllerFile($this->basePath . '/Foo/Application/News/DetailDescription.php');

        $template->expects($this->at(0))
                ->method('hasFile')
                ->will($this->returnValue(true));

        $template->expects($this->at(1))
                ->method('isAbsoluteFile')
                ->will($this->returnValue(true));

        $template->expects($this->at(2))
                ->method('get')
                ->will($this->returnValue('/foo/template'));

        $template->expects($this->at(3))
                ->method('set')
                ->with($this->equalTo('/foo/template.' . $writer->getFileExtension()));

        $template->expects($this->at(4))
                ->method('setDir')
                ->with($this->equalTo(null));

        $template->expects($this->at(5))
                ->method('isFileAvailable')
                ->will($this->returnValue(true));

        $actual = $writer->write($this->getRecord());
    }

    /**
     * If the template engine cant resolve a template file we generate a
     * presentation using an generator if available
     */
    public function testFallbackGenerator()
    {
        $template = $this->createMock('PSX\Framework\Template\TemplateInterface');
        $router   = $this->getMockBuilder('PSX\Framework\Loader\ReverseRouter')
            ->disableOriginalConstructor()
            ->getMock();

        $template->expects($this->at(0))
                ->method('hasFile')
                ->will($this->returnValue(true));

        $template->expects($this->at(1))
                ->method('isAbsoluteFile')
                ->will($this->returnValue(false));

        $template->expects($this->at(2))
                ->method('setDir')
                ->with($this->equalTo($this->basePath . '/Foo/Resource'));

        $template->expects($this->at(3))
                ->method('isFileAvailable')
                ->will($this->returnValue(false));

        $writer = $this->getWriter($template, $router);
        $writer->setControllerFile($this->basePath . '/Foo/Application/News/DetailDescription.php');

        $actual = $writer->write($this->getRecord());

        preg_match('/<body>(.*)<\/body>/ims', $actual, $matches);

        $this->assertXmlStringEqualsXmlString($this->getExpectedFallbackTemplate(), $matches[1]);
    }

    protected function getExpectedFallbackTemplate()
    {
        return <<<HTML
<dl data-name="record">
	<dt>id</dt>
	<dd>1</dd>
	<dt>author</dt>
	<dd>foo</dd>
	<dt>title</dt>
	<dd>bar</dd>
	<dt>content</dt>
	<dd>foobar</dd>
	<dt>date</dt>
	<dd>2012-03-11T13:37:21Z</dd>
</dl>
HTML;
    }

    public function getRecord()
    {
        $record = new Record();
        $record->id = 1;
        $record->author = 'foo';
        $record->title = 'bar';
        $record->content = 'foobar';
        $record->date = new DateTime('2012-03-11 13:37:21');

        return $record;
    }

    public function getResultSet()
    {
        $entries = array();

        $record = new Record();
        $record->id = 1;
        $record->author = 'foo';
        $record->title = 'bar';
        $record->content = 'foobar';
        $record->date = new DateTime('2012-03-11 13:37:21');

        $entries[] = $record;

        $record = new Record();
        $record->id = 2;
        $record->author = 'foo';
        $record->title = 'bar';
        $record->content = 'foobar';
        $record->date = new DateTime('2012-03-11 13:37:21');

        $entries[] = $record;

        $record = new Record('collection');
        $record->totalResults = 2;
        $record->startIndex = 0;
        $record->itemsPerPage = 8;
        $record->entry = $entries;

        return $record;
    }

    public function getComplexRecord()
    {
        $actor = new Record();
        $actor->id = 'tag:example.org,2011:martin';
        $actor->objectType = 'person';
        $actor->displayName = 'Martin Smith';
        $actor->url = 'http://example.org/martin';

        $object = new Record();
        $object->id = 'tag:example.org,2011:abc123/xyz';
        $object->url = 'http://example.org/blog/2011/02/entry';

        $target = new Record();
        $target->id = 'tag:example.org,2011:abc123';
        $target->objectType = 'blog';
        $target->displayName = 'Martin\'s Blog';
        $target->url = 'http://example.org/blog/';

        $activity = new Record('activity');
        $activity->verb = 'post';
        $activity->actor = $actor;
        $activity->object = $object;
        $activity->target = $target;
        $activity->published = new DateTime('2011-02-10T15:04:55Z');

        return $activity;
    }

    public function getEmptyRecord()
    {
        return new Record('record', array());
    }
}
