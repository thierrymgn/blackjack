<?php

namespace App\Service\PlayerRound;

use App\DTO\Response\Error;
use App\Entity\PlayerRound;
use App\Entity\Round;
use App\Entity\User;
use App\Form\PlayerRound\WagerType;
use App\Repository\PlayerRoundRepository;
use App\Repository\RoundRepository;
use App\Service\Form\FormService;
use App\Service\Round\RoundCardService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;

class PlayerRoundService
{

    private PlayerRoundRepository $playerRoundRepository;
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private RoundCardService $roundCardService;

    public function __construct(PlayerRoundRepository $playerRoundRepository, EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, RoundCardService $roundCardService)
    {
        $this->playerRoundRepository = $playerRoundRepository;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->roundCardService = $roundCardService;
    }
   
    public function addNewPlayerRoundToRound(User $user, Round $round): PlayerRound
    {
        $playerRound = new PlayerRound();
        $playerRound->setCreationDate(new \DateTimeImmutable());
        $playerRound->setLastUpdateDate(new \DateTimeImmutable());
        $playerRound->setUser($user);
        $playerRound->setRound($round);
        $playerRound->setStatus('created');
        $playerRound->setWager(0);
        $this->entityManager->getRepository(PlayerRound::class)->save($playerRound);

        return $playerRound;
    }

    public function wageRound(User $user, string $uuid, array $payload): array
    {
        $playerRound = $this->playerRoundRepository->findOneById($uuid);
        if (empty($playerRound)) {
            return [null, new Error(['error' => 'Round not found'], 404)];
        }

        if ($user !== $playerRound->getUser()) {
            return [null, new Error(['error' => 'You are not allowed to play this round'], 403)];
        }

        if ($playerRound->getStatus() !== 'created') {
            return [null, new Error(['error' => 'You already waged this round'], 409)];
        }

        $form = $this->formFactory->create(WagerType::class, $playerRound, ['currentWallet' => $user->getWallet()]);
        $form->submit($payload);

        if (!$form->isValid()) {
            return [null, new Error(['error' => 'Invalid payload', 'errors' => FormService::getFormErrors($form)], 400)];
        }

        $playerRound->setWager($payload['wager']);
        $playerRound->setLastUpdateDate(new \DateTimeImmutable());
        $playerRound->setStatus('waged');

        $this->entityManager->getRepository(PlayerRound::class)->save($playerRound);

        $user->setWallet($user->getWallet() - $payload['wager']);
        $user->setLastUpdateDate(new \DateTimeImmutable());
        
        $this->entityManager->getRepository(User::class)->save($user, false);

        return [$playerRound, null];
    }

    public function getRound(User $user, string $uuid): array
    {
        
        $playerRound = $this->playerRoundRepository->findOneById($uuid);

        if(empty($playerRound)) {
            return [null, new Error(['error' => 'Round not found'], 404)];
        }

        if ($user !== $playerRound->getUser()) {
            return [null, new Error(['error' => 'You are not allowed to play this round'], 403)];
        }

        return [$playerRound, null];
    }

    public function hitRound(User $user, string $uuid): array
    {
        $playerRound = $this->playerRoundRepository->findOneById($uuid);

        if(empty($playerRound)) {
            return [null, new Error(['error' => 'Round not found'], 404)];
        }

        if ($user !== $playerRound->getUser()) {
            return [null, new Error(['error' => 'You are not allowed to play this round'], 403)];
        }

        if ($playerRound->getRound()->getStatus() !== 'playing') {
            return [null, new Error(['error' => 'The round has not started yet'], 409)];
        }

        if ($playerRound->getStatus() !== 'playing') {
            return [null, new Error(['error' => 'You can not hit this round', 'status' => $playerRound->getStatus()], 409)];
        }
        $drawnCards = $this->roundCardService->drawCards($playerRound->getRound(), 1);
        $playerRound->addToCurrentCards($drawnCards);

        $score = $this->roundCardService->calculateScore($playerRound->getCurrentCards());

        if ($score > 21) {
            $playerRound->setStatus('busted');
        } elseif ($score === 777) {
            $playerRound->setStatus('blackjack');
        }

        $playerRound->setLastUpdateDate(new \DateTimeImmutable());

        $this->entityManager->getRepository(PlayerRound::class)->save($playerRound, false);

        return [$playerRound, null];
    }

    public function standRound(User $user, string $uuid): array
    {
        $playerRound = $this->playerRoundRepository->findOneById($uuid);

        if(empty($playerRound)) {
            return [null, new Error(['error' => 'Round not found'], 404)];
        }

        if ($user !== $playerRound->getUser()) {
            return [null, new Error(['error' => 'You are not allowed to play this round'], 403)];
        }

        if ($playerRound->getRound()->getStatus() !== 'playing') {
            return [null, new Error(['error' => 'The round has not started yet'], 409)];
        }

        if ($playerRound->getStatus() !== 'playing') {
            return [null, new Error(['error' => 'You can not stand this round', 'status' => $playerRound->getStatus()], 409)];
        }

        $playerRound->setLastUpdateDate(new \DateTimeImmutable());
        $playerRound->setStatus('standed');

        $this->entityManager->getRepository(PlayerRound::class)->save($playerRound, false);

        return [$playerRound, null];
    }

    public function getPlayerRoundStatus(PlayerRound $playerRound, int $dealerScore): string
    {
        $playerScore = $this->roundCardService->calculateScore($playerRound->getCurrentCards());

        if($playerScore > 21) {
            return 'busted';
        }

        if($playerScore === $dealerScore) {
            return 'draw';
        }

        if($playerScore === 777) {
            return 'blackjack';
        }

        if($playerScore > $dealerScore) {
            return 'won';
        }
        
        return 'lost';
    }

    public function calculateGainsForPlayerRound(PlayerRound $playerRound): int
    {
        $wager = $playerRound->getWager();
        if($playerRound->getStatus() === 'won') {
            return $wager * 2;
        }

        if($playerRound->getStatus() === 'blackjack') {
            return round($wager * 2.5);
        }

        if($playerRound->getStatus() === 'draw') {
            return $wager;
        }

        return 0;
    }
}