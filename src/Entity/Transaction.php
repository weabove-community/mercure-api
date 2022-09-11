<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private string $txHash;

    #[ORM\Column]
    private int $timestamp;

    #[ORM\Column]
    private string $function;

    #[ORM\Column]
    private string $ticker;

    #[ORM\Column]
    private string $identifier;

    #[ORM\Column]
    private string $receiver;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Transaction
     */
    public function setId(int $id): Transaction
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTxHash(): string
    {
        return $this->txHash;
    }

    /**
     * @param string $txHash
     * @return Transaction
     */
    public function setTxHash(string $txHash): Transaction
    {
        $this->txHash = $txHash;
        return $this;
    }


    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     * @return Transaction
     */
    public function setTimestamp(int $timestamp): Transaction
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return string
     */
    public function getFunction(): string
    {
        return $this->function;
    }

    /**
     * @param string $function
     * @return Transaction
     */
    public function setFunction(string $function): Transaction
    {
        $this->function = $function;
        return $this;
    }

    /**
     * @return string
     */
    public function getTicker(): string
    {
        return $this->ticker;
    }

    /**
     * @param string $ticker
     * @return Transaction
     */
    public function setTicker(string $ticker): Transaction
    {
        $this->ticker = $ticker;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     * @return Transaction
     */
    public function setIdentifier(string $identifier): Transaction
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getReceiver(): string
    {
        return $this->receiver;
    }

    /**
     * @param string $receiver
     * @return Transaction
     */
    public function setReceiver(string $receiver): Transaction
    {
        $this->receiver = $receiver;
        return $this;
    }
}
