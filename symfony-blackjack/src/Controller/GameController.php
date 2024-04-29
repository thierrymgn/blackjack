<?php

namespace App\Controller;

use App\Service\Game\GameService;
use Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GameController extends AbstractController
{
    private GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    #[Route('/game', name: 'create_game', methods: ['POST'])]
    public function createGame(): JsonResponse
    {
        list($game, $err) = $this->gameService->createGame($this->getUser());

        if ($err !== null) {
            return $this->json($err->getContent(), $err->getCode());
        }

        return $this->json($game, 201, [], ['groups' => ['game']]);
    }

    #[Route('/game', name: 'get_game_list', methods: ['GET'])]
    public function getGameList(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $limit = $request->query->get('limit', 12);
        $page = $request->query->get('page', 0);

        list($games, $err) = $this->gameService->getPaginatedGameList($limit, $page);

        return $this->json($games, 201, [], ['groups' => ['game']]);
    }


    #[Route('/game/{gameId}', name: 'get_game', methods: ['GET'])]
    public function getGame(string $gameId): JsonResponse
    {
        $user = $this->getUser();
        list($game, $err) = $this->gameService->getGame($user, $gameId);

        if ($err !== null) {
            return $this->json($err->getContent(), $err->getCode());
        }

        return $this->json($game, 200, [], ['groups' => ['game']]);
    }

    #[Route('/game/{gameId}/stop', name: 'stop_game', methods: ['PATCH'])]
    public function stopGame(string $gameId): JsonResponse
    {
        $user = $this->getUser();
        list($game, $err) = $this->gameService->stopGame($user, $gameId);

        if ($err !== null) {
            return $this->json($err->getContent(), $err->getCode());
        }

        return $this->json($game, 200, [], ['groups' => ['game']]);
    }

    #[Route('/game/{gameId}/newround', name: 'add_round_to_game', methods: ['PATCH'])]
    public function addRoundToGame(string $gameId): JsonResponse
    {
        $user = $this->getUser();
        list($game, $err) = $this->gameService->addRoundToGame($user, $gameId);

        if ($err !== null) {
            return $this->json($err->getContent(), $err->getCode());
        }

        return $this->json($game, 200, [], ['groups' => ['game']]);
    }

    #[Route('/game/{gameId}', name: 'delete_game', methods: ['DELETE'])]
    public function deleteGame(string $gameId): Response
    {
        $user = $this->getUser();
        list($game, $err) = $this->gameService->deleteGame($user, $gameId);

        if ($err !== null) {
            return $this->json($err->getContent(), $err->getCode());
        }

        return $this->json([], 204);
    }
}