<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Rank::class)]
class Rank
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\OneToOne(targetEntity: Token::class, mappedBy: 'rank')]
    private Token $token;

    #[ORM\Column(nullable: true)]
    private float|null $score;

    #[ORM\Column(nullable: true)]
    private int|null $rank;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Rank
     */
    public function setId(int $id): Rank
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getScore(): ?float
    {
        return $this->score;
    }

    /**
     * @param float|null $score
     * @return Rank
     */
    public function setScore(?float $score): Rank
    {
        $this->score = $score;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRank(): ?int
    {
        return $this->rank;
    }

    /**
     * @param int|null $rank
     * @return Rank
     */
    public function setRank(?int $rank): Rank
    {
        $this->rank = $rank;
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
     * @return Rank
     */
    public function setToken(Token $token): Rank
    {
        $this->token = $token;
        return $this;
    }
}
