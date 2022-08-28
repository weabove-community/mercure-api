<?php

namespace App\Entity;

use App\Repository\CollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CollectionRepository::class)]
#[UniqueEntity('collection')]
class Collection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(unique: true, type: 'string', length: 255)]
    private string $collection;

    #[ORM\OneToMany(targetEntity: Nft::class, mappedBy: 'collection')]
    private $nfts;

    #[ORM\Column(length: 127)]
    private string $type;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $ticker;

    #[ORM\Column(length: 255)]
    private string $owner;

    public function __construct()
    {
        $this->nfts = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getCollection(): string
    {
        return $this->collection;
    }

    /**
     * @param string $collection
     * @return Collection
     */
    public function setCollection(string $collection): self
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getNfts(): ArrayCollection
    {
        return $this->nfts;
    }

    /**
     * @param Nft $nft
     * @return Collection
     */
    public function addNft(Nft $nft): Collection
    {
        $this->nfts->add($nft);
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Collection
     */
    public function setType($type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Collection
     */
    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getTicker(): string
    {
        return $this->ticker;
    }

    /**
     * @param string $ticker
     * @return Collection
     */
    public function setTicker($ticker): self
    {
        $this->ticker = $ticker;
        return $this;
    }

    /**
     * @return string
     */
    public function getOwner(): string
    {
        return $this->owner;
    }

    /**
     * @param string $owner
     * @return Collection
     */
    public function setOwner($owner): self
    {
        $this->owner = $owner;
        return $this;
    }
}
