<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttributeToken::class)]
class AttributeToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Attribute::class, inversedBy: 'attributeTokens')]
    private $attribute;

    #[ORM\ManyToOne(targetEntity: Token::class, inversedBy: 'attributeTokens')]
    private  $token;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return AttributeToken
     */
    public function setId(int $id): AttributeToken
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param mixed $attribute
     * @return AttributeToken
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return AttributeToken
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }



}