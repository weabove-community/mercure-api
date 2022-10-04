<?php

namespace App\Service;

use App\Entity\Collection;
use App\Entity\Token;

class TokenService
{
    public function create(Collection $collection, $tokenNumber, string $name, string $imageUrl): Token
    {
        $token = new Token();
        $token
            ->setToken($tokenNumber)
            ->setName($name)
            ->setImageUrl($imageUrl)
            ->setCollection($collection);

        return $token;
    }
}