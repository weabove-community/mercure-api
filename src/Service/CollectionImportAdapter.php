<?php

namespace App\Service;

use App\Entity\Collection;

use App\Service\Blockchain\ERC20\CollectionImport as CollectionImportErc20;


class CollectionImportAdapter
{
    private array $collectionImports;
    private CollectionImportErc20 $collectionImportErc20;

    public function __construct(
        CollectionImportErc20 $collectionImportErc20
    )
    {
        $this->collectionImports = [
            $collectionImportErc20
        ];
    }

    public function getCollectionImport(Collection $collection)
    {
        foreach ($this->collectionImports as $service) {
            if ($service->isSupport($collection)) {
                return $service;
            }
        }

        throw new \Exception(sprintf('Cant handle collection from blockchain %s', $collection->getBlockchain()));
    }
}