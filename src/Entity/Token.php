<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     "self",
 *     href = "expr('/api/tokens/' ~ object.getCollection().getIdentifier() ~ '/' ~ object.getToken())"
 * )
 */
#[ORM\Entity(repositoryClass: Token::class)]
class Token
{
    #[JMS\Exclude]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[JMS\Expose]
    #[ORM\Column]
    private int $token;

    #[JMS\Expose]
    #[ORM\Column]
    private string $name;

    #[JMS\Exclude]
    #[ORM\OneToOne(targetEntity: Rank::class, mappedBy: 'token')]
    private Rank|null $rank;

    #[JMS\Exclude]
    #[ORM\OneToMany(targetEntity: TokenAttribute::class, mappedBy: 'token')]
    private $tokenAttributes;

    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'tokens', cascade: ['persist'])]
    private Collection $collection;

    #[JMS\Expose]
    #[ORM\Column]
    private string $imageUrl;

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Token
     */
    public function setName(string $name): Token
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTokenAttributes()
    {
        return $this->tokenAttributes;
    }

    /**
     * @param TokenAttribute $tokenAttribute
     * @return void
     */
    public function addTokenAttribute(TokenAttribute $tokenAttribute)
    {
        $this->tokenAttributes->add($tokenAttribute);
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
     * @return Token
     */
    public function setCollection(Collection $collection): Token
    {
        $this->collection = $collection;
        $collection->addToken($this);

        return $this;
    }

    /**
     * @return Rank|null
     */
    public function getRank(): ?Rank
    {
        return $this->rank;
    }

    /**
     * @param Rank|null $rank
     * @return Token
     */
    public function setRank(?Rank $rank): Token
    {
        $this->rank = $rank;
        return $this;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     * @return Token
     */
    public function setImageUrl(string $imageUrl): Token
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }
}