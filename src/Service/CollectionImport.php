<?php

namespace App\Service;

use App\Entity\Collection;
use App\Enum\BlockchainEnum;
use App\Enum\CollectionStatusEnum;
use App\Repository\AttributeRepository;
use App\Repository\CollectionRepository;
use App\Service\Blockchain\ERC20\CollectionImport as ERC20CollectionImport;
use App\Service\Model\CollectionImportInterface;
use Doctrine\ORM\EntityManagerInterface;

class CollectionImport
{
    private CollectionImportInterface $collectionImport;

    private EntityManagerInterface $em;

    private AttributeRepository $attributeRepository;

    public function __construct(
        EntityManagerInterface $em,
        CollectionRepository $collectionRepository,
        FileSystem $fileSystem,
        AttributeRepository $attributeRepository
    )
    {
        $this->em = $em;
        $this->collectionRepository = $collectionRepository;
        $this->fileSystem = $fileSystem;
        $this->attributeRepository = $attributeRepository;
    }

    private function initCollectionImport(string $blockchain): CollectionImportInterface
    {
        switch ($blockchain) {
            case BlockchainEnum::ERC20->value:
                return new ERC20CollectionImport($this->fileSystem, $this->em, $this->attributeRepository);
            default:
                Throw new \InvalidArgumentException(sprintf('cant handle blockchain %s', $blockchain));
        }
    }

    public function run(Collection $collection)
    {
        $this->collectionImport = $this->initCollectionImport($collection->getBlockchain());

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
