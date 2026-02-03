<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SubscribeController extends AbstractController
{
    #[Route('/subscribe', name: 'app_subscribe')]
    public function __invoke(): Response
    {
        return $this->render('subscribe/index.html.twig');
    }
}
