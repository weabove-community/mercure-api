<?php

namespace App\Command;

use App\Entity\Collection;
use App\Enum\BlockchainEnum;
use App\Enum\CollectionStatusEnum;
use App\Repository\CollectionRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[AsCommand(name: 'app:collection:add')]
class CollectionCreateCommand extends Command
{
    public function __construct(CollectionRepository $collectionRepository)
    {
        $this->collectionRepository = $collectionRepository;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('blockchain', InputArgument::REQUIRED, 'Blockchain (ERC20, Elrond)')
            ->addArgument('project', InputArgument::REQUIRED, 'Project name')
            ->addArgument('identifier', InputArgument::REQUIRED, 'Smart contrart or identifier collection')
            ->addArgument('ipfs', InputArgument::REQUIRED, 'Ipfs', null)
            ->addArgument('extension-picture', InputArgument::REQUIRED, 'File metadata extension', null)
            ->addOption('extension-metadata', 'f', InputOption::VALUE_OPTIONAL, 'File metadata extension', null)
            ->addOption('first', 's', InputOption::VALUE_OPTIONAL, 'First token', null)
            ->addOption('last', 'l', InputOption::VALUE_OPTIONAL, 'Last token', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $blockchain = BlockchainEnum::tryFrom($input->getArgument('blockchain'));
        if (null === $blockchain) {
            $output->writeln(sprintf('Unknow blockchain \'%s\'', $input->getArgument('blockchain')));
            return Command::INVALID;
        }

        $collection = new Collection();
        $collection
            ->setBlockchain($blockchain->value)
            ->setName($input->getArgument('project'))
            ->setIdentifier($input->getArgument('identifier'))
            ->setIpfs($input->getArgument('ipfs'))
            ->setPictureExtension($input->getArgument('extension-picture'))
            ->setStatus(CollectionStatusEnum::ADDED->value)
        ;

        if (null !== $input->getOption('extension-metadata')) {
            $collection->setTraitFileExtension($input->getOption('extension-metadata'));
        }

        if (null !== $input->getOption('first')) {
            $collection->setStartId($input->getOption('first'));
        }

        if (null !== $input->getOption('last')) {
            $collection->setEndId($input->getOption('last'));
        }

        $this->collectionRepository->save($collection, true);

        return Command::SUCCESS;
    }
}
