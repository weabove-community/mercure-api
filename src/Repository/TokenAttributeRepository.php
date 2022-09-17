<?php

namespace App\Repository;

use App\Entity\Token;
use App\Entity\TokenAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TokenAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenAttribute::class);
    }

    /**
     * @param Token $token
     * @return mixed
     */
    public function findByToken(Token $token)
    {
        return $this->createQueryBuilder('ta')
            ->leftjoin('ta.attribute', 'a')
            ->leftJoin('a.traitType', 'tt')
            ->where('ta.token = :token')
            ->orderBy('tt.name')
            ->setParameter('token', $token)
            ->getQuery()->getResult();
    }
}
