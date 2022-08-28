<?php

namespace App\Entity;

use App\Repository\NftRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NftRepository::class)]
class Nft
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(unique: true, type: 'string', length: 255)]
    private $identifier;

    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'Nfts')]
    private $collection;

    #[ORM\Column(length: 127)]
    private $type;

    #[ORM\Column(length: 255)]
    private $name;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Nft
     */
    public function setId(int $id): Nft
    {
        $this->id = $id;
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
     * @return Nft
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return ?Collection
     */
    public function getCollection(): ?Collection
    {
        return $this->collection;
    }

    /**
     * @param Collection $collection
     * @return Nft
     */
    public function setCollection(Collection $collection): self
    {
        $this->collection = $collection;
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
     * @return Nft
     */
    public function setType($type): self
    {
        $this->type = $type;
        return $this;
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
     * @return Nft
     */
    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }


}
