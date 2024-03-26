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

    public function __construct(EntityManagerInterface $entityManager, RoundService $roundService)
    {
        $this->entityManager = $entityManager;
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

    public function getPaginatedGameList(int $limit, int $page): Success | Error
    {
        $gameRepository = $this->entityManager->getRepository(Game::class);
        $games = $gameRepository->findBy([], [], $limit, $page * $limit);

        return new Success(['games' => $games], 200);
    }

    public function getGame(User $user, string $gameId): Success | Error
    {
        $game = $this->entityManager->getRepository(Game::class)->findOneBy(['id' => $gameId]);

        if (empty($game)) {
            return new Error(['error' => 'Game not found'], 404);
        }

        if (!$game->getUsers()->contains($user) && !in_array('ROLE_ADMIN', $user->getRoles())){
            return new Error(['error' => 'User not in game'], 403);
        }

        return new Success(['game' => $game], 200);
    }

    public function deleteGame(User $user, string $gameId): Success | Error
    {
        $game = $this->entityManager->getRepository(Game::class)->findOneBy(['id' => $gameId]);

        if (empty($game)) {
            return new Error(['error' => 'Game not found'], 404);
        }

        if (!$game->getUsers()->contains($user) && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return new Error(['error' => 'User not in game'], 403);
        }

        $this->entityManager->remove($game);
        $this->entityManager->flush();

        return new Success([], 204);
    }
}