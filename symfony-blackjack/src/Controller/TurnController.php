<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TurnController extends AbstractController
{
    #[Route('/turn', name: 'app_turn')]
    public function index(): Response
    {
        return $this->render('turn/index.html.twig', [
            'controller_name' => 'TurnController',
        ]);
    }
}
