<?php

namespace App\Service\PlayerRound;

use App\DTO\Response\Error;
use App\DTO\Response\Success;
use App\Entity\PlayerRound;
use App\Entity\Round;
use App\Entity\User;
use App\Form\PlayerRound\WagerType;
use App\Repository\PlayerRoundRepository;
use App\Repository\RoundRepository;
use App\Service\Form\FormService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;

class PlayerRoundService
{

    private PlayerRoundRepository $playerRoundRepository;
    private RoundRepository $roundRepository;
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;

    public function __construct(PlayerRoundRepository $playerRoundRepository, RoundRepository $roundRepository, EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->playerRoundRepository = $playerRoundRepository;
        $this->roundRepository = $roundRepository;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
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
        
        $globalRound = $playerRound->getRound();
        //$this->roundService->startRound($globalRound);

        return [$playerRound, null];
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