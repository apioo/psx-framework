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

namespace PSX\Framework\Command\Messenger;

use PSX\Framework\Messenger\DefaultTransport;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Transport\SetupableTransportInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * Based on the Symfony package. For the full copyright and license information,
 * please take a look at https://github.com/symfony/symfony
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Vincent Touzet <vincent.touzet@gmail.com>
 * @author Christoph Kappestein <christoph.kappestein@gmail.com>
 */
#[AsCommand(name: 'messenger:setup-transports', description: 'Prepare the required infrastructure for the transport')]
class SetupTransportCommand extends Command
{
    private TransportInterface $transport;

    public function __construct(TransportInterface $transport)
    {
        parent::__construct();

        $this->transport = $transport;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->transport instanceof SetupableTransportInterface) {
            $io->note(\sprintf('The "%s" transport does not support setup.', DefaultTransport::NAME));
            return self::FAILURE;
        }

        try {
            $this->transport->setup();

            $io->success(\sprintf('The "%s" transport was set up successfully.', DefaultTransport::NAME));
        } catch (\Exception $e) {
            throw new \RuntimeException(\sprintf('An error occurred while setting up the "%s" transport: ', DefaultTransport::NAME).$e->getMessage(), 0, $e);
        }

        return self::SUCCESS;
    }
}
