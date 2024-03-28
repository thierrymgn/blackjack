<?php

namespace App\DTO\Card;

use Symfony\Component\Serializer\Annotation\Groups;

class CardDTO
{
    #[Groups(['playerRound', 'game', 'round'])]
    private ?string $value = null;

    #[Groups(['playerRound', 'game', 'round'])]
    private ?string $suit = null;

    public function __construct(string $value, string $suit)
    {
        $this->value = $value;
        $this->suit = $suit;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getSuit(): ?string
    {
        return $this->suit;
    }
}
