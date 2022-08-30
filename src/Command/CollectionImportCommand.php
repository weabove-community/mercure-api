<?php

namespace App\Command;

use App\Entity\Collection;
use App\Entity\Nft;
use Symfony\Component\Console\Attribute\AsCommand;
use App\ElrondApi\CollectionService;
use App\Repository\CollectionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'elrond:collection-import')]
class CollectionImportCommand extends Command
{
    /** @var CollectionService  */
    private $service;
    /** @var CollectionRepository */
    private $repository;
    /** @var EntityManager */
    private $entityManager;

    public function __construct(CollectionService $collectionService,
                                CollectionRepository $repository,
                                ManagerRegistry $doctrine)
    {
        $this->service = $collectionService;
        $this->repository = $repository;
        $this->entityManager = $doctrine->getManager();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('collection', InputArgument::REQUIRED, 'Code collection')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // INNOVATOR-fca3a7     Drifters
        // GNS-4b9bdf           ElrondCity Genesis
        $code = $input->getArgument('collection');

        $collection = $this->importCollection($code);

        $this->importNftByCollection($collection);

        return Command::SUCCESS;
    }

    protected function importCollection($code): Collection
    {
        $response = $this->service->get($code);
        $params = json_decode($response->getBody()->getContents(), true);
        $collection = new Collection();
        $collection
            ->setCollection($params['collection'])
            ->setName($params['name'])
            ->setOwner($params['owner'])
            ->setTicker($params['ticker'])
            ->setType($params['ticker']);
        $this->entityManager->persist($collection);
        $this->entityManager->flush();

        return $collection;
    }

    protected function importNftByCollection(Collection $collection)
    {
        $code = $collection->getCollection();
        $response = $this->service->count($code);
        $count = $response->getBody()->getContents();
        $floor = ceil($count/100);

        $query = [
            'from' => 0,
            'size' => 100
        ];
        for ($i = 0; $i <= $floor; $i++) {
            $response = $this->service->getNftsCollection($code, $query);
            $items = json_decode($response->getBody()->getContents(), true);
            foreach ($items as $item) {
                $nft = new Nft();
                $nft->setCollection($collection)
                    ->setIdentifier($item['identifier'])
                    ->setName($item['name'])
                    ->setType($item['type']);
                $this->entityManager->persist($nft);
            }
            $this->entityManager->flush();
            $query['from'] += 100;
        }
    }
}
