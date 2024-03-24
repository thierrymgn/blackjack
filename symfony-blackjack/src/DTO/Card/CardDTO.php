<?php

namespace App\DTO\Card;


class CardDTO
{
    private ?string $value = null;

    private ?string $suit = null;

    public function __construct(string $value, string $suit)
    {
        $this->value = $value;
        $this->suit = $suit;
    }
}
