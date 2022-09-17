<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Token::class)]
class Token
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $token;

    #[ORM\OneToMany(targetEntity: TokenAttribute::class, mappedBy: 'token')]
    private $tokenAttributes;

    #[ORM\Column(nullable: true)]
    private float $score;

    #[ORM\Column(nullable: true)]
    private int $rank;

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
    public function getToken(): int
    {
        return $this->token;
    }

    /**
     * @param int $token
     * @return Token
     */
    public function setToken(int $token): Token
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTokenAttributes(): ArrayCollection
    {
        return $this->tokenAttributes;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * @param float $score
     * @return Token
     */
    public function setScore(float $score): Token
    {
        $this->score = $score;
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