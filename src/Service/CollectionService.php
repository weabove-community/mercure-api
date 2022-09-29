<?php

namespace App\Service;

use App\Entity\Collection;

class CollectionService
{
    public function create(string $blockchain, string $identifier, string $projectName, string $ipfs): Collection
    {
        $collection = new Collection();
        $collection
            ->setIdentifier($identifier)
            ->setBlockchain($blockchain)
            ->setIpfs($ipfs)
            ->setName($projectName);

        return $collection;
    }
}
