<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * SetupTransportCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org
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

        if ($this->transport instanceof SetupableTransportInterface) {
            $this->transport->setup();

            $io->success(sprintf('The "%s" transport was set up successfully.', DefaultTransport::NAME));
        } else {
            $io->note(sprintf('The "%s" transport does not support setup.', DefaultTransport::NAME));
        }

        return 0;
    }
}
