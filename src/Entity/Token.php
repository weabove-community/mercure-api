<?php

namespace App\Entity;
use App\Repository\TokenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
class Token
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $number;

    #[ORM\Column(nullable: true)]
    private $rank;

    #[ORM\OneToMany(targetEntity: AttributeToken::class, mappedBy: 'token', cascade: ["persist"])]
    private ArrayCollection $attributeTokens;

    public function __construct()
    {
        $this->attributeTokens = new ArrayCollection();
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
     * @return Token
     */
    public function setId(int $id): Token
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return Token
     */
    public function setNumber(int $number): Token
    {
        $this->number = $number;
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

    /**
     * @return int
     */
    public function getRank(): int
    {
        return $this->rank;
    }

    /**
     * @param int $rank
     * @return Token
     */
    public function setRank(int $rank): Token
    {
        $this->rank = $rank;
        return $this;
    }







}