<?php

namespace App\Controller;

use App\Service\PlayerRound\PlayerRoundService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PlayerRoundController extends AbstractController
{

    private PlayerRoundService $playerRoundService;

    public function __construct(PlayerRoundService $playerRoundService)
    {
        $this->playerRoundService = $playerRoundService;
    }

    #[Route('/player/round/{uuid}/wage', name: 'start_round', methods: ['POST'])]
    public function wageRound(string $uuid, Request $request, SerializerInterface $serializer): Response
    {
        $user = $this->getUser();       
        $payload = json_decode($request->getContent(), true);

        $response = $this->playerRoundService->wageRound($user, $uuid, $payload);

        $jsonObject = $serializer->serialize($response->getContent(), 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/player/round/{uuid}', name: 'get_round', methods: ['GET'])]
    public function getRound(string $uuid, SerializerInterface $serializer): Response
    {
        $user = $this->getUser();
        $response = $this->playerRoundService->getRound($user, $uuid);

        $jsonObject = $serializer->serialize($response->getContent(), 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/player/round/{uuid}/hit', name: 'hit_round', methods: ['POST'])]
    public function hitRound(): JsonResponse
    {
        $user = $this->getUser();
        

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PlayerRoundController.php',
        ]);
    }

    #[Route('/player/round/{uuid}/stand', name: 'stand_round', methods: ['POST'])]
    public function standRound(): JsonResponse
    {
        $user = $this->getUser();
        

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PlayerRoundController.php',
        ]);
    }
}
