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
    private $id;

    #[ORM\Column]
    private string $type;

    #[ORM\Column]
    private string $value;

    #[ORM\Column]
    private float $rarity;

    #[ORM\OneToMany(targetEntity: AttributeToken::class, mappedBy: 'attribute', cascade: ["persist"])]
    private ArrayCollection $attributeTokens;



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Attribute
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return Attribute
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Attribute
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttributeTokens()
    {
        return $this->attributeTokens;
    }

    /**
     * @param AttributeToken $attributeToken
     * @return Token
     */
    public function addAttributeToken(AttributeToken $attributeToken)
    {
        $this->attributeTokens->add($attributeToken);
        $attributeToken->setToken($this);
        return $this;
    }


}
