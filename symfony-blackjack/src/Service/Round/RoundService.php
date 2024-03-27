<?php

namespace App\Service\Round;

use App\DTO\Card\CardDTO;
use App\DTO\Response\Error;
use App\DTO\Response\Success;
use App\Entity\Game;
use App\Entity\Round;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class RoundService
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function createRound(Game $game): Success | Error
    {
        $round = new Round();
        $round->setCreationDate(new \DateTimeImmutable());
        $round->setLastUpdateDate(new \DateTimeImmutable());
        $round->setCardsLeft($this->generateDeck());
        $round->setGame($game);
        $round->setStatus('started');

        $this->entityManager->getRepository(Round::class)->save($round);
        $this->logger->info('Round created', ['round' => $round]);

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

    public function startRound(Round $round): Success | Error
    {
        if (!$this->hasAllPlayerRoundBeenWaged($round)) {
            return new Error(['error' => 'Not all player rounds have been waged'], 400);
        }

        $round = $this->setCards($round);

        $this->entityManager->getRepository(Round::class)->save($round, false);

        return new Success(['round' => $round], 200);
    }

    public function hasAllPlayerRoundBeenWaged(Round $round): bool
    {
        $playerRounds = $round->getPlayerRounds();

        foreach ($playerRounds as $playerRound) {
            if($playerRound->getStatus() !== 'waged') {
                return false;
            }
        }

        return true;
    }

    public function setCards(Round $round): Round
    {
        $cards = $round->getCardsLeft();
        $playerRounds = $round->getPlayerRounds();

        foreach ($playerRounds as $playerRound) {
            $playerRound->setCurrentCards(array_splice($cards, 0, 2));
            $playerRound->setStatus('playing');
            $playerRound->setLastUpdateDate(new \DateTimeImmutable());
        }

        $round->setDealerCards(array_splice($cards, 0, 1));

        $round->setCardsLeft($cards);
        $round->setStatus('playing');

        $round->getGame()->setLastUpdateDate(new \DateTimeImmutable());
        $round->getGame()->setStatus('playing');

        return $round;
    }
}