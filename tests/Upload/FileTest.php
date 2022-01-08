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

namespace PSX\Framework\Framework\Tests\Upload;

use PHPUnit\Framework\TestCase;
use PSX\Framework\Upload\File;
use PSX\Framework\Upload\Exception;

/**
 * FileTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FileTest extends TestCase
{
    public function testFile()
    {
        $data = array(
            'name'     => 'upload.txt',
            'type'     => 'text/plain',
            'size'     => 12,
            'tmp_name' => '/tmp/tmp123',
            'error'    => UPLOAD_ERR_OK,
        );

        $file = $this->getMockBuilder('PSX\Framework\Upload\File')
            ->setMethods(array('isUploadedFile', 'moveUploadedFile'))
            ->getMock();

        $file->expects($this->once())
            ->method('isUploadedFile')
            ->with('/tmp/tmp123')
            ->will($this->returnValue(true));

        $file->expects($this->once())
            ->method('moveUploadedFile')
            ->with('/tmp/tmp123', '/foo/bar')
            ->will($this->returnValue(true));

        $file->setFile($data);

        $this->assertEquals('upload.txt', $file->getName());
        $this->assertEquals('text/plain', $file->getType());
        $this->assertEquals(12, $file->getSize());
        $this->assertEquals('/tmp/tmp123', $file->getTmpName());
        $this->assertEquals(UPLOAD_ERR_OK, $file->getError());

        $file->move('/foo/bar');
    }

    public function testFileIniSize()
    {
        $this->expectException(Exception::class);

        $this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_INI_SIZE);
    }

    public function testFileFormSize()
    {
        $this->expectException(Exception::class);

        $this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_FORM_SIZE);
    }

    public function testFilePartial()
    {
        $this->expectException(Exception::class);

        $this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_PARTIAL);
    }

    public function testFileNoFile()
    {
        $this->expectException(Exception::class);

        $this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_NO_FILE);
    }

    public function testFileNoTmpDir()
    {
        $this->expectException(Exception::class);

        $this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_NO_TMP_DIR);
    }
    
    public function testFileCantWrite()
    {
        $this->expectException(Exception::class);

        $this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_CANT_WRITE);
    }

    public function testFileExtension()
    {
        $this->expectException(Exception::class);

        $this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_EXTENSION);
    }

    public function testFileUnknown()
    {
        $this->expectException(Exception::class);

        $this->createFile('foo', 'text/plain', 12, 'bar', -1);
    }

    public function testFromEnvironmentInvalidUpload()
    {
        $this->expectException(Exception::class);

        $key = uniqid('upload_');

        $_FILES[$key] = [
            'name'     => 'upload.txt',
            'type'     => 'text/plain',
            'size'     => 12,
            'tmp_name' => '/tmp/tmp123',
            'error'    => UPLOAD_ERR_OK,
        ];

        // this call fails on the is_upload_file check
        File::fromEnvironment($key);
    }

    public function testFromEnvironmentNotExisting()
    {
        $this->expectException(Exception::class);

        File::fromEnvironment('foo');
    }

    protected function createFile($name, $type, $size, $tmpName, $error)
    {
        return new File(array(
            'name'     => $name,
            'type'     => $type,
            'size'     => $size,
            'tmp_name' => $tmpName,
            'error'    => $error,
        ));
    }
}
