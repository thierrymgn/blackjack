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
}