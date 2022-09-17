<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TraitType::class)]
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
     * @param Attribute $attribute
     * @return void
     */
    public function addAttribute(Attribute $attribute): void
    {
        $this->attributes->add($attribute);
        $attribute->setTraitType($this);
    }

    /**
     * @return ArrayCollection
     */
    public function getAttributes(): ArrayCollection
    {
        return $this->attributes;
    }
}