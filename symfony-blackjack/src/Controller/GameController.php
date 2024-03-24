<?php

namespace App\Controller;

use App\Service\Game\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function createGame(SerializerInterface $serializer): Response
    {
        $response = $this->gameService->createGame($this->getUser());

        $jsonObject = $serializer->serialize($response, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new Response($jsonObject, $response->getCode(), ['Content-Type' => 'application/json']);
    }

    #[Route('/game', name: 'get_game_list', methods: ['GET'])]
    public function getGameList(Request $request, SerializerInterface $serializer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $limit = $request->query->get('limit', 12);
        $page = $request->query->get('page', 0);

        $response = $this->gameService->getPaginatedGameList($limit, $page);

        $jsonObject = $serializer->serialize($response, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new Response($jsonObject, $response->getCode(), ['Content-Type' => 'application/json']);
    }


    #[Route('/game/{gameId}', name: 'get_game', methods: ['GET'])]
    public function getGame(string $gameId, SerializerInterface $serializer): Response
    {
        $user = $this->getUser();
        $response = $this->gameService->getGame($user, $gameId);

        $jsonObject = $serializer->serialize($response, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new Response($jsonObject, $response->getCode(), ['Content-Type' => 'application/json']);
    }

    #[Route('/game/{gameId}', name: 'delete_game', methods: ['DELETE'])]
    public function deleteGame(string $gameId, SerializerInterface $serializer): Response
    {
        $user = $this->getUser();
        $deleteGameResponse = $this->gameService->deleteGame($user, $gameId);
        return $this->json($deleteGameResponse->getContent(), $deleteGameResponse->getCode());
    }
}