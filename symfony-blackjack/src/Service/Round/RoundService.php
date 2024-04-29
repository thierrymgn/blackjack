<?php

namespace App\Service\Round;

use App\DTO\Response\Error;
use App\Entity\Game;
use App\Entity\Round;
use App\Service\PlayerRound\PlayerRoundService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class RoundService
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private PlayerRoundService $playerRoundService;
    private RoundCardService $roundCardService;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, PlayerRoundService $playerRoundService, RoundCardService $roundCardService)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->playerRoundService = $playerRoundService;
        $this->roundCardService = $roundCardService;
    }

    public function addNewRoundToGame(Game $game): Round
    {
        $round = new Round();
        $round->setCreationDate(new \DateTimeImmutable());
        $round->setLastUpdateDate(new \DateTimeImmutable());
        $round->setCardsLeft($this->generateDeck());
        $round->setGame($game);
        $round->setStatus('started');

        $game->addRound($round);
        $this->entityManager->getRepository(Round::class)->save($round, true);

        $this->logger->info('Round created', ['round' => $round]);

        foreach ($game->getUsers() as $user) {
            $playerRound = $this->playerRoundService->addNewPlayerRoundToRound($user, $round);
            $round->addPlayerRound($playerRound);
        }

        $this->entityManager->getRepository(Round::class)->save($round, false);

        return $round;
    }

    private function generateDeck(): array
    {
        $deck = [];
        $suits = ['heart', 'diamond', 'club', 'spade'];
        $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $deck[] = [$suit, $value];
            }
        }

        shuffle($deck);

        return $deck;
    }

    public function startRound(Round $round): array
    {
        if (!$this->hasAllPlayerRoundBeenWaged($round)) {
            return new Error(['error' => 'Not all player rounds have been waged'], 400);
        }

        $round = $this->setCards($round);

        $this->entityManager->getRepository(Round::class)->save($round, false);

        return [$round, null];
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
        $playerRounds = $round->getPlayerRounds();

        foreach ($playerRounds as $playerRound) {
            $playerRound->setStatus('playing');
            $drawnCards = $this->roundCardService->drawCards($round, 2);
            $playerRound->addToCurrentCards($drawnCards);

            $score = $this->roundCardService->calculateScore($playerRound->getCurrentCards());

            if ($score > 21) {
                $playerRound->setStatus('busted');
            } elseif ($score === 777) {
                $playerRound->setStatus('blackjack');
            }
            $playerRound->setLastUpdateDate(new \DateTimeImmutable());
        }

        $drawnCards = $this->roundCardService->drawCards($round, 1);
        $round->addToDealerCards($drawnCards);

        $round->setStatus('playing');

        $round->getGame()->setLastUpdateDate(new \DateTimeImmutable());
        $round->getGame()->setStatus('playing');

        return $round;
    }

    public function stopRound(Round $round): array
    {
        list($allPlayerRoundAreDone, $err) = $this->hasAllPlayerRoundBeenFinished($round);
        if (!$allPlayerRoundAreDone) {
            return [$round, null];
        }

        $round->setStatus('ended');
        $round->setLastUpdateDate(new \DateTimeImmutable());

        $this->entityManager->getRepository(Round::class)->save($round, false);

        return [$round, null];
    }

    public function hasAllPlayerRoundBeenFinished(Round $round): array
    {
        $playerRounds = $round->getPlayerRounds();

        foreach ($playerRounds as $playerRound) {
            if($playerRound->getStatus() === 'playing') {
                return [false, null];
            }
        }

        return [true, null];
    }

    public function drawDealerCards(Round $round): array
    {
        while ($this->roundCardService->calculateScore($round->getDealerCards()) < 17) {
            $drawnCards = $this->roundCardService->drawCards($round, 1);
            $round->addToDealerCards($drawnCards);
        }

        $round->setLastUpdateDate(new \DateTimeImmutable());

        $this->entityManager->getRepository(Round::class)->save($round, false);

        return [$round, null];
    }

    public function distributeGains(Round $round): array
    {
        if($round->getStatus() !== 'ended') {
            return [$round, new Error(['error' => 'The round has not ended yet'], 409)];
        }

        $dealerScore = $this->roundCardService->calculateScore($round->getDealerCards());
        foreach ($round->getPlayerRounds() as $playerRound) {
            $playerRoundStatus = $this->playerRoundService->getPlayerRoundStatus($playerRound, $dealerScore);
            $playerRound->setStatus($playerRoundStatus);

            $gain = $this->playerRoundService->calculateGainsForPlayerRound($playerRound);
            $playerRound->setGains($gain);
            $playerRound->getUser()->setWallet($playerRound->getUser()->getWallet() + $gain);

            $playerRound->setLastUpdateDate(new \DateTimeImmutable());
        }

        return [$round, null];
    }
}