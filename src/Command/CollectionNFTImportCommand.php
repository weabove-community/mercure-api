<?php

namespace App\Command;

use App\Repository\CollectionRepository;
use App\Service\CollectionImport;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

#[AsCommand(name: 'app:metadata:process')]
class CollectionNFTImportCommand extends Command
{
    /** @var CollectionRepository  */
    private $collectionRepository;

    /** @var CollectionImport  */
    private $collectionImport;

    public function __construct(
        CollectionRepository $collectionRepository,
        CollectionImport $collectionImport
    )
    {
        $this->collectionRepository = $collectionRepository;
        $this->collectionImport = $collectionImport;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('identifier', InputArgument::REQUIRED, 'Smart contrart or identifier collection')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $collection = $this->collectionRepository->findOneByIdentifier($input->getArgument('identifier'));
        if ($collection === null) {
            throw new NotFoundResourceException('Collection identifier not found');
        }

        try {
            $this->collectionImport->run($collection);
        } catch (\Exception $e) {
            throw new \Exception('Error import collection: ' . $e->getMessage());
        }
        $output->writeln(sprintf('Import NFT collection %s finished', $input->getArgument('identifier')));
        return Command::SUCCESS;
    }
}
