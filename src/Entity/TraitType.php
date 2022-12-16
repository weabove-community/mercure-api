<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class TraitType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private string $name;

    #[ORM\OneToMany(targetEntity: Attribute::class, mappedBy: 'traitType')]
    private $attributes;

    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'traitTypes', cascade: ['persist'])]
    private Collection $collection;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
    }
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     * @return TraitType
     */
    public function setName(string $name): TraitType
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function addAttribute(Attribute $attribute): void
    {
        $this->attributes->add($attribute);
    }

    /**
     * @param $attributes
     * @return $this
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * @param Collection $collection
     * @return TraitType
     */
    public function setCollection(Collection $collection): TraitType
    {
        $this->collection = $collection;
        $this->collection->addTraitType($this);

        return $this;
    }
}
