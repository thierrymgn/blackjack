<?php

namespace App\Service\Round;

use App\Entity\Round;
use Doctrine\ORM\EntityManagerInterface;

class RoundCardService 
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
   
    public function drawCards(Round $round, int $amount): array
    {
        $cards = $round->getCardsLeft();
        $drawnCards = array_splice($cards, 0, $amount);

        $round->setCardsLeft($cards);

        $this->entityManager->getRepository(Round::class)->save($round, false);

        return $drawnCards;
    }

    public function calculateScore($currentCards): int
    {
        $score = 0;
        $figuresCount = 0;
        $acesCount = 0;
        $aceValue = 11;

        foreach($currentCards as $card) {
            $value = $card[1];
            if(is_numeric($value)) {
                $score += $value;
            } elseif ($value === 'A') {
                $acesCount++;
            } else {
                $score += 10; 
                $figuresCount++;
            }
        }

        for($i = 0; $i < $acesCount; $i++) {
            if ($score + $aceValue > 21) {
                $score += 1;
            } else {
                $score += $aceValue;
            }
        }

        // if there are 2 cards in hand, and one of them is an ace, and the other one is a figure, then it's a blackjack
        if (count($currentCards) === $figuresCount + $acesCount) {
            return 777;
        }

        return $score;
    }
}