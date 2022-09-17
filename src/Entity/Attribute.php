<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Attribute::class)]
class Attribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: TraitType::class, inversedBy: 'attributes', cascade: ['persist'])]
    private TraitType $traitType;

    #[ORM\Column]
    private string $value;

    #[ORM\OneToMany(targetEntity: TokenAttribute::class, mappedBy: 'attribute')]
    private $tokenAttributes;

    #[ORM\Column(nullable: true)]
    private float $percent;

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
     * @return TraitType
     */
    public function getTraitType(): TraitType
    {
        return $this->traitType;
    }

    /**
     * @param TraitType $traitType
     * @return Attribute
     */
    public function setTraitType(TraitType $traitType): self
    {
        $this->traitType = $traitType;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Attribute
     */
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return float
     */
    public function getPercent(): float
    {
        return $this->percent;
    }

    /**
     * @param float $percent
     * @return Attribute
     */
    public function setPercent(float $percent): Attribute
    {
        $this->percent = $percent;
        return $this;
    }

    public function getTokenAttributes(): ArrayCollection
    {
        return $this->tokenAttributes;
    }

    public function addTokenAttribute(TokenAttribute $tokenAttribute)
    {
        $this->tokenAttributes->add($tokenAttribute);
    }



}
