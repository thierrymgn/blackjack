<?php

namespace App\Service\Game;

use App\DTO\Response\Error;
use App\DTO\Response\Success;
use App\Entity\Game;
use App\Entity\User;
use App\Service\Round\RoundService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;

class GameService
{
    private EntityManagerInterface $entityManager;
    private RoundService $roundService;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, RoundService $roundService)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->roundService = $roundService;
    }

    public function createGame(User $user): Success | Error
    {
        $game = new Game();
        $game->setCreationDate(new \DateTimeImmutable());
        $game->setLastUpdateDate(new \DateTimeImmutable());
        $game->addUser($user);
        // add the dealer
        $game->setStatus('created');
        $this->entityManager->persist($game);

        $this->entityManager->flush();

        $this->roundService->createRound($game);

        return new Success(['game' => $game], 201);
    }
}