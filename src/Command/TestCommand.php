<?php

namespace App\Command;

use App\Entity\Collection;

use App\Service\Alchemy\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'test:test')]
class TestCommand extends Command
{
    public function __construct(
        Client $client
    )
    {
        $this->client = $client;
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->client->getFloorPrice('0xd0aaac09e7f9b794fafa9020a34ad5b906566a5c');
        dump($response->getBody()->getContents());
        return Command::SUCCESS;
    }
}
