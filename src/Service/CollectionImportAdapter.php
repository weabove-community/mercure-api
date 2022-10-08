<?php

namespace App\Service;

use App\Entity\Collection;

use App\Service\Blockchain\ERC20\CollectionImport as CollectionImportErc20;
use App\Service\Blockchain\Elrond\CollectionImport as CollectionImportElrond;


class CollectionImportAdapter
{
    private array $collectionImports;
    private CollectionImportErc20 $collectionImportErc20;
    private CollectionImportElrond $collectionImportElrond;

    public function __construct(
        CollectionImportErc20 $collectionImportErc20,
        CollectionImportElrond $collectionImportElrond
    )
    {
        $this->collectionImports = [
            $collectionImportErc20,
            $collectionImportElrond,
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