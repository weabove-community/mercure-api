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

    public function findByCollectionIdentifier($identifier, $limit = 50, $options=array())
    {
        $query = $this->createQueryBuilder('t')
            ->leftJoin('t.collection', 'c')
        ;

        return $query
            ->andWhere('c.identifier = :identifier')
            ->setParameter('identifier', $identifier)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $identifier
     * @param $token
     * @return
     */
    public function findOneByCollectionIdentifierAndToken($identifier, $token)
    {
        $query = $this->createQueryBuilder('t')
            ->leftJoin('t.collection', 'c')
            ->leftJoin('t.tokenAttributes', 'ta')
            ->leftJoin('ta.attribute', 'a')
            ->leftJoin('a.traitType', 'tt')
        ;

        return $query
            ->andWhere('c.identifier = :identifier')
            ->andWhere('t.token = :token')
            ->setParameter('identifier', $identifier)
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
