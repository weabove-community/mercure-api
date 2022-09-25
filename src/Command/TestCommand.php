<?php

namespace App\Command;

use App\Entity\Collection;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'test:test')]
class TestCommand extends Command
{
    public function __construct(
        \App\Service\Blockchain\ERC20\CollectionImport $collectionImport
    )
    {
        $this->collectionImport = $collectionImport;
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $collection = new Collection();
        $collection
            ->setName('WeAbove')
            ->setIpfs('bafybeiani764y53yslrmu2lpyf7ek3idtabvlxvdjbco4sdygtezedagui')
            ->setBlockchain('ethereum')
            ->setIdentifier('0xd0aaac09e7f9b794fafa9020a34ad5b906566a5c')
        ;
        $this->collectionImport->importMetadata($collection);
        return Command::SUCCESS;
    }
}
