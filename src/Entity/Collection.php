<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Collection::class)]
class Collection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private string $type; // NFT ou SFT

    #[ORM\Column]
    private string $name;

    #[ORM\Column]
    private string $blockchain;

    #[ORM\Column]
    private string $ipfs;

    #[ORM\Column]
    private string $identifier;

    #[ORM\OneToMany(targetEntity: Token::class, mappedBy: 'collection')]
    private string $tokens;

    public function __construct()
    {
        $this->tokens = new ArrayCollection();
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
     * @return Collection
     */
    public function setId(int $id): Collection
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Collection
     */
    public function setType(string $type): Collection
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getBlockchain(): string
    {
        return $this->blockchain;
    }

    /**
     * @param string $blockchain
     * @return Collection
     */
    public function setBlockchain(string $blockchain): Collection
    {
        $this->blockchain = $blockchain;
        return $this;
    }

    /**
     * @return string
     */
    public function getIpfs(): string
    {
        return $this->ipfs;
    }

    /**
     * @param string $ipfs
     * @return Collection
     */
    public function setIpfs(string $ipfs): Collection
    {
        $this->ipfs = $ipfs;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     * @return Collection
     */
    public function setIdentifier(string $identifier): Collection
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getTokens(): ArrayCollection
    {
        return $this->tokens;
    }

    public function addToken(Token $token): void
    {
        $this->tokens->add($token);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Collection
     */
    public function setName(string $name): Collection
    {
        $this->name = $name;
        return $this;
    }
}
