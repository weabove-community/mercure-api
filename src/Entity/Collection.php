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
    private string $name;

    #[ORM\Column]
    private string $blockchain;

    #[ORM\Column]
    private string $status;

    #[ORM\Column]
    private string $ipfs;

    #[ORM\Column(unique: true)]
    private string $identifier;

    #[ORM\OneToMany(targetEntity: Token::class, mappedBy: 'collection')]
    private $tokens;

    #[ORM\Column(nullable: true)]
    private string|null $traitFileExtension = null;

    #[ORM\Column(nullable: true)]
    private string|null $pictureExtension = null;

    #[ORM\Column(nullable: true)]
    private int|null $startId;

    #[ORM\Column(nullable: true)]
    private int|null $endId;

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

    /**
     * @return string
     */
    public function getTraitFileExtension(): string
    {
        return $this->traitFileExtension;
    }

    /**
     * @param string $traitFileExtension
     * @return Collection
     */
    public function setTraitFileExtension(string $traitFileExtension): Collection
    {
        $this->traitFileExtension = $traitFileExtension;
        return $this;
    }

    /**
     * @return int
     */
    public function getStartId(): int
    {
        return $this->startId;
    }

    /**
     * @param int $startId
     * @return Collection
     */
    public function setStartId(int $startId): Collection
    {
        $this->startId = $startId;
        return $this;
    }

    /**
     * @return int
     */
    public function getEndId(): int
    {
        return $this->endId;
    }

    /**
     * @param int $endId
     * @return Collection
     */
    public function setEndId(int $endId): Collection
    {
        $this->endId = $endId;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Collection
     */
    public function setStatus(string $status): Collection
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPictureExtension(): ?string
    {
        return $this->pictureExtension;
    }

    /**
     * @param string|null $pictureExtension
     * @return Collection
     */
    public function setPictureExtension(?string $pictureExtension): Collection
    {
        $this->pictureExtension = $pictureExtension;
        return $this;
    }
}
