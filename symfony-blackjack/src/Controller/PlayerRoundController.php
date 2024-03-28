<?php

namespace App\Controller;

use App\Service\PlayerRound\PlayerRoundService;
use App\Service\Round\RoundService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PlayerRoundController extends AbstractController
{

    private PlayerRoundService $playerRoundService;
    private RoundService $roundService;

    public function __construct(PlayerRoundService $playerRoundService, RoundService $roundService)
    {
        $this->playerRoundService = $playerRoundService;
        $this->roundService = $roundService;
    }

    #[Route('/player-round/{uuid}/wage', name: 'start_round', methods: ['PATCH'])]
    public function wageRound(string $uuid, Request $request): JsonResponse
    {
        $user = $this->getUser();       
        $payload = json_decode($request->getContent(), true);

        if(null === $payload) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }

        list($playerRound, $err) = $this->playerRoundService->wageRound($user, $uuid, $payload);

        if ($err !== null) {
            return $this->json($err->getContent(), $err->getCode());
        }

        list($round, $err) = $this->roundService->startRound($playerRound->getRound());

        return $this->json($playerRound, 200, [], ['groups' => ['playerRound']]);
    }

    #[Route('/player-round/{uuid}', name: 'get_round', methods: ['GET'])]
    public function getRound(string $uuid, SerializerInterface $serializer): Response
    {
        $user = $this->getUser();
        list($playerRound, $err) = $this->playerRoundService->getRound($user, $uuid);
        if ($err !== null) {
            return $this->json($err->getContent(), $err->getCode());
        }

        return $this->json($playerRound, 200, [], ['groups' => ['playerRound']]);
    }

    #[Route('/player-round/{uuid}/hit', name: 'hit_round', methods: ['PATCH'])]
    public function hitRound(string $uuid): JsonResponse
    {
        $user = $this->getUser();
        list($playerRound, $err) = $this->playerRoundService->hitRound($user, $uuid);
        if ($err !== null) {
            return $this->json($err->getContent(), $err->getCode());
        }

        return $this->json($playerRound, 200, [], ['groups' => ['playerRound']]);
    }

    #[Route('/player-round/{uuid}/stand', name: 'stand_round', methods: ['PATCH'])]
    public function standRound(string $uuid): JsonResponse
    {
        $user = $this->getUser();
        list($playerRound, $err) = $this->playerRoundService->standRound($user, $uuid);
        if ($err !== null) {
            return $this->json($err->getContent(), $err->getCode());
        }

        return $this->json($playerRound, 200, [], ['groups' => ['playerRound']]);
    }
}
