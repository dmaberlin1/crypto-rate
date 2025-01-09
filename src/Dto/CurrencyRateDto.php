<?php

namespace App\Dto;

use DateTimeInterface;

class CurrencyRateDto
{
    private string $pair;
    private float $rate;
    private DateTimeInterface $updatedAt;

    public function __construct(string $pair, float $rate, DateTimeInterface $updatedAt)
    {
        $this->pair = $pair;
        $this->rate = $rate;
        $this->updatedAt = $updatedAt;
    }

    public function getPair(): string
    {
        return $this->pair;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        // Преобразуем объект DateTime в строку
        return [
            'pair' => $this->pair,
            'rate' => $this->rate,
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }

    // Дополнительный метод для легкого создания DTO из массива
    public static function fromArray(array $data): self
    {
        return new self(
            $data['pair'],
            $data['rate'],
            new \DateTime($data['updatedAt']) // Преобразуем строку в DateTime
        );
    }
}