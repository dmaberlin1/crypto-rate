<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CurrencyRate
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 10)]
    private string $pair;

    #[ORM\Column(type: "float")]
    private float $rate;

    #[ORM\Column(type: "datetime")]
    private \DateTime $updatedAt;

    // Getters and Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getPair(): string
    {
        return $this->pair;
    }

    public function setPair(string $pair): void
    {
        $this->pair = $pair;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}