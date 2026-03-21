<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GameEventController extends AbstractController
{
    #[Route('/game/event', name: 'app_game_event')]
    public function index(): Response
    {
        return $this->render('game_event/index.html.twig', [
            'controller_name' => 'GameEventController',
        ]);
    }
}
