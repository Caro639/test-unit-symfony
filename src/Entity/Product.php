<?php

namespace App\Entity;

class Product
{
    const FOOD_PRODUCT = 'food';

    private string $name;
    private string $type;
    private float $price;

    public function __construct(string $name, string $type, float $price)
    {
        $this->name = $name;
        $this->type = $type;
        $this->price = $price;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function computeTVA(): float
    {
        if ($this->price < 0) {
            throw new \Exception('The TVA cannot be negative.');
        }

        if (self::FOOD_PRODUCT === $this->type) {
            return $this->price * 0.055;
        }

        // TVA standard pour les autres produits (19.6%)
        return $this->price * 0.196;
    }
}
