<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Token::class)]
class Token
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $tokenId;

    #[ORM\OneToMany(targetEntity: TokenAttribute::class, mappedBy: 'token')]
    private $tokenAttributes;

    public function __construct()
    {
        $this->tokenAttributes = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Token
     */
    public function setId(int $id): Token
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getTokenId(): int
    {
        return $this->tokenId;
    }

    /**
     * @param int $token
     * @return Token
     */
    public function setTokenId(int $tokenId): Token
    {
        $this->tokenId = $tokenId;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTokenAttributes(): ArrayCollection
    {
        return $this->tokenAttributes;
    }


}