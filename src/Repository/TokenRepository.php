<?php

namespace App\Repository;

use App\Entity\Token;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }

    public function findByTokenIdsAndCollection($identifier, $tokenIds)
    {
        $query = $this->createQueryBuilder('t')
            ->leftJoin('t.collection', 'c')
            ->leftJoin('t.tokenAttributes', 'ta')
            ->leftJoin('ta.attribute', 'a')
        ;

        return $query
            ->andWhere('t.token in (:tokens)')
            ->andWhere('c.identifier = :identifier')
            ->setParameter('tokens', $tokenIds)
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getResult()
        ;
    }
}
