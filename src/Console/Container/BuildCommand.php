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

namespace PSX\Framework\Console\Container;

use Doctrine\Common\Annotations\Reader;
use Psr\Container\ContainerInterface;
use PSX\Dependency\Compiler\PhpCompiler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * BuildCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class BuildCommand extends Command
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    public function __construct(ContainerInterface $container, Reader $reader)
    {
        parent::__construct();

        $this->container = $container;
        $this->reader = $reader;
    }

    protected function configure()
    {
        $this
            ->setName('container:build')
            ->setDescription('Compiles the DI container');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $compiler = new PhpCompiler($this->reader, 'Container', '');

        $code = $compiler->compile($this->container);
        $code.= '$container = new Container();' . "\n";
        $code.= '$container->setParameter(\'config.file\', __DIR__ . \'/../configuration.php\');' . "\n";
        $code.= 'return $container;' . "\n";

        $file = PSX_PATH_CACHE . '/container.compiled.php';
        file_put_contents($file, $code);

        $output->writeln('Write compiled container to: ' . $file);
        $output->writeln('To use the container you need to include the file at index.php');
        $output->writeln('');

        return 0;
    }
}
