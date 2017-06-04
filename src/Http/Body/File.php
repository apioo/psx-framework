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

namespace PSX\Framework\Http\Body;

use PSX\Http\ResponseInterface;

/**
 * File
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class File extends Body
{
    /**
     * @var string
     */
    protected $fileName;

    public function __construct($file, $fileName = null, $contentType = null)
    {
        parent::__construct($file, $contentType);

        $this->fileName = $fileName;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function writeTo(ResponseInterface $response)
    {
        $file = $this->data;

        $fileName = $this->fileName;
        if (empty($fileName)) {
            $fileName = pathinfo($file, PATHINFO_FILENAME);
        }

        $contentType = $this->contentType;
        if ($contentType === null) {
            $contentType = mime_content_type($file);
        }

        $response->setHeader('Content-Type', $contentType);
        $response->setHeader('Content-Disposition', 'attachment; filename="' . addcslashes($fileName, '"') . '"');
        $response->getBody()->write(file_get_contents($file));
    }
}
