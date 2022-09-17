<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TokenAttribute::class)]
class TokenAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Attribute::class, inversedBy: 'tokenAttributes', cascade: ['persist'])]
    private Attribute $attribute;

    #[ORM\ManyToOne(targetEntity: Token::class, inversedBy: 'tokenAttributes', cascade: ['persist'])]
    private Token $token;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TokenAttribute
     */
    public function setId(int $id): TokenAttribute
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Attribute
     */
    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    /**
     * @param Attribute $attribute
     * @return TokenAttribute
     */
    public function setAttribute(Attribute $attribute): TokenAttribute
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
    }

    /**
     * @param Token $token
     * @return TokenAttribute
     */
    public function setToken(Token $token): TokenAttribute
    {
        $this->token = $token;
        return $this;
    }

}