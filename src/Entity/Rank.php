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

    #[ORM\OneToOne(targetEntity: Token::class, inversedBy: 'rank')]
    private Token $token;

    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'rank')]
    private Collection $collection;

    #[ORM\Column(nullable: true)]
    private float|null $handoScore;

    #[ORM\Column(nullable: true)]
    private int|null $handoRank;

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
        $token->setRank($this);
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
     * @return Rank
     */
    public function setCollection(Collection $collection): Rank
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getHandoScore(): ?float
    {
        return $this->handoScore;
    }

    /**
     * @param float|null $handoScore
     * @return Rank
     */
    public function setHandoScore(?float $handoScore): Rank
    {
        $this->handoScore = $handoScore;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHandoRank(): ?int
    {
        return $this->handoRank;
    }

    /**
     * @param int|null $handoRank
     * @return Rank
     */
    public function setHandoRank(?int $handoRank): Rank
    {
        $this->handoRank = $handoRank;
        return $this;
    }
}
