<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class PlayerRoundController extends AbstractController
{
    #[Route('/round/{uuid}/start', name: 'start_round', methods: ['POST'])]
    public function startRound(): JsonResponse
    {
        $user = $this->getUser();
        
        //get wage

        //

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PlayerRoundController.php',
        ]);
    }

    #[Route('/round/{uuid}/hit', name: 'hit_round', methods: ['POST'])]
    public function hitRound(): JsonResponse
    {
        $user = $this->getUser();
        

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PlayerRoundController.php',
        ]);
    }

    #[Route('/round/{uuid}/stand', name: 'stand_round', methods: ['POST'])]
    public function standRound(): JsonResponse
    {
        $user = $this->getUser();
        

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PlayerRoundController.php',
        ]);
    }
}
