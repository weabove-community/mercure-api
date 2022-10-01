<?php

namespace App\Service;

use App\Entity\Collection;
use App\Enum\CollectionStatusEnum;
use App\Service\Model\CollectionImportInterface;
use Doctrine\ORM\EntityManagerInterface;

class CollectionImport
{
    private CollectionImportInterface $collectionImport;
    private EntityManagerInterface $em;
    private CollectionImportAdapter $adapter;

    public function __construct(
        EntityManagerInterface $em,
        CollectionImportAdapter $adapter
    )
    {
        $this->em = $em;
        $this->adapter = $adapter;
    }

    public function run(Collection $collection)
    {
        $this->collectionImport = $this->adapter->getCollectionImport($collection);

        try {
            while (CollectionStatusEnum::RANK_EXECUTED->value !== $collection->getStatus()) {
                switch ($collection->getStatus()) {
                    case CollectionStatusEnum::ADDED->value:
                        $this->collectionImport->importMetadata($collection);
                        break;
                    case CollectionStatusEnum::METADATA_IMPORTED->value:
                        $this->collectionImport->saveTrait($collection);
                        break;
                    case CollectionStatusEnum::TRAIT_SAVED->value:
                        $this->collectionImport->processTokenAttributes($collection);
                        break;
                    case CollectionStatusEnum::TOKEN_ATTRIBUTE_SAVED->value:
                        $this->collectionImport->processRank($collection);
                        break;
                    default:
                        throw new \Exception('Unknow status ' . $collection->getStatus());
                }
            }
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Run collection %s with status %s failed : %s',
                $collection->getName(),
                $collection->getStatus(),
                $e->getMessage()
            ));
        }

        $this->em->persist($collection);
        $this->em->flush();
    }
}
