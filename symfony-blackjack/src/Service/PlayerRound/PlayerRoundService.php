<?php

namespace App\Service\PlayerRound;

use App\DTO\Response\Error;
use App\DTO\Response\Success;
use App\Entity\PlayerRound;
use App\Entity\User;
use App\Form\StartRoundType;
use App\Repository\PlayerRoundRepository;
use App\Repository\RoundRepository;
use App\Service\Form\FormService;
use App\Service\Round\RoundService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;

class PlayerRoundService
{

    private PlayerRoundRepository $playerRoundRepository;
    private RoundRepository $roundRepository;
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private RoundService $roundService;

    public function __construct(PlayerRoundRepository $playerRoundRepository, RoundRepository $roundRepository, EntityManagerInterface $entityManager,  FormFactoryInterface $formFactory, RoundService $roundService)
    {
        $this->playerRoundRepository = $playerRoundRepository;
        $this->roundRepository = $roundRepository;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->roundService = $roundService;
    }
    
    public function wageRound(User $user, string $uuid, array $payload): Success | Error
    {
        $round = $this->roundRepository->findOneById($uuid);
        if (empty($round)) {
            return new Error(['error' => 'Round not found'], 404);
        }

        if (!in_array($user, $round->getGame()->getUsers()->toArray())) {
            return new Error(['error' => 'You are not allowed to play this round'], 403);
        }

        $playerRoundAlreadyExists = $this->playerRoundRepository->findOneBy([
            'round' => $round, 
            'user' => $user
        ]);

        if($playerRoundAlreadyExists) {
            return new Error(['error' => 'You already played this round'], 400);
        }

        $playerRound = new PlayerRound();

        $form = $this->formFactory->create(StartRoundType::class, $playerRound, ['currentWallet' => $user->getWallet()]);
        $form->submit($payload);

        if (!$form->isValid()) {
            return new Error(['error' => 'Invalid payload', 'errors' => FormService::getFormErrors($form)], 400);
        }

        $playerRound->setWager($payload['wager']);
        $playerRound->setCreationDate(new \DateTimeImmutable());
        $playerRound->setLastUpdateDate(new \DateTimeImmutable());
        $playerRound->setUser($user);
        $playerRound->setRound($round);
        $playerRound->setStatus('waged');

        $this->entityManager->getRepository(PlayerRound::class)->save($playerRound);
        
        $globalRound = $playerRound->getRound();
        $this->roundService->startRound($globalRound);

        return new Success(['round' => $playerRound], 200);
    }

    public function getRound(User $user, string $uuid): Success | Error
    {
        $round = $this->roundRepository->findOneById($uuid);
        if (empty($round)) {
            return new Error(['error' => 'Round not found'], 404);
        }

        if (!in_array($user, $round->getGame()->getUsers()->toArray())) {
            return new Error(['error' => 'You are not allowed to play this round'], 403);
        }

        $playerRound = $this->playerRoundRepository->findOneBy([
            'round' => $round, 
            'user' => $user
        ]);

        if(empty($playerRound)) {
            return new Error(['error' => 'You did not play this round'], 404);
        }

        return new Success(['round' => $playerRound], 200);
    }
}