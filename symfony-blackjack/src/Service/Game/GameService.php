<?php

namespace App\Service\Game;

use App\DTO\Response\Error;
use App\DTO\Response\Success;
use App\Entity\Game;
use App\Entity\User;
use App\Service\Round\RoundService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class GameService
{
    private EntityManagerInterface $entityManager;
    private RoundService $roundService;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, RoundService $roundService, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->roundService = $roundService;
        $this->logger = $logger;
    }

    public function createGame(User $user): array
    {
        if ($user->getWallet() < 1) {
            $error = new Error(['error' => 'Not enough money to create a game'], 400);
            $this->logger->error('Game not created', ['error' => $error->getContent()]);
            return [null, $error];
        }

        $game = new Game();
        $game->setCreationDate(new \DateTimeImmutable());
        $game->setLastUpdateDate(new \DateTimeImmutable());
        $game->addUser($user);
        $game->setStatus('created');
        $this->entityManager->getRepository(Game::class)->save($game);
        $this->logger->info('Game created', ['game' => $game]);
        
        $this->roundService->addNewRoundToGame($game);

        return [$game, null];
    }

    /**
     * @return Game[]
     */
    public function getPaginatedGameList(int $limit, int $page): array
    {
        $gameRepository = $this->entityManager->getRepository(Game::class);
        return [$gameRepository->findBy([], [], $limit, $page * $limit), null];
    }

    public function getGame(User $user, string $gameId): array
    {
        $game = $this->entityManager->getRepository(Game::class)->findOneBy(['id' => $gameId]);

        if (empty($game)) {
            return [null, new Error(['error' => 'Game not found'], 404)];
        }

        if (!$game->getUsers()->contains($user) && !in_array('ROLE_ADMIN', $user->getRoles())){
            return [null, new Error(['error' => 'User not in game'], 403)];
        }

        return [$game, null];
    }

    public function deleteGame(User $user, string $gameId): array
    {
        $game = $this->entityManager->getRepository(Game::class)->findOneBy(['id' => $gameId]);

        if (empty($game)) {
            return [null, new Error(['error' => 'Game not found'], 404)];
        }

        if (!$game->getUsers()->contains($user) && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return [null, new Error(['error' => 'User not in game'], 403)];
        }

        $this->entityManager->remove($game);
        $this->entityManager->flush();

        return [null, null];
    }
}