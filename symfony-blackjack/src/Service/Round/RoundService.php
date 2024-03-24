<?php

namespace App\Service\Round;

use App\DTO\Card\CardDTO;
use App\DTO\Response\Error;
use App\DTO\Response\Success;
use App\Entity\Game;
use App\Entity\Round;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;

class RoundService
{
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function createRound(Game $game): Success | Error
    {
        $round = new Round();
        $round->setCreationDate(new \DateTimeImmutable());
        $round->setLastUpdateDate(new \DateTimeImmutable());
        $round->setCardsLeft($this->generateDeck());
        $round->setGame($game);
        $round->setStatus('started');

        $this->entityManager->persist($round);
        $this->entityManager->flush();

        return new Success(['round' => $round], 201);
    }

    private function generateDeck(): array
    {
        $deck = [];
        $suits = ['hearts', 'diamonds', 'clubs', 'spades'];
        $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $deck[] = new CardDTO($suit, $value);
            }
        }

        shuffle($deck);

        return $deck;
    }


}