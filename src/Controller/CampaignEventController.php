<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CampaignEventController extends AbstractController
{
    #[Route('/events/overview', name: 'app_campaign_event')]
    public function index(): Response
    {
        return $this->render('campaign_event/index.html.twig', [
            'controller_name' => 'CampaignEventController',
        ]);
    }
}
