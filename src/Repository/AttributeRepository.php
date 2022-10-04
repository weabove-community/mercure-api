<?php

namespace App\Repository;

use App\Entity\Attribute;
use App\Entity\Collection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attribute::class);
    }

    public function getByCollection(Collection $collection, bool $withValue)
    {
        $query = $this->createQueryBuilder('a')
            ->leftJoin('a.tokenAttributes', 'ta');
        if ($withValue) {
            $query->where('a.value is not null');
        } else {
            $query->where('a.value is null');
        }
        return $query->andWhere('a.collection = :collection')
            ->setParameter('collection', $collection)
            ->getQuery()
            ->getResult()
        ;
    }
}
