<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/informations', name: 'app_informations')]
class InformationsController extends AbstractController
{
    #[Route('/', name: 'app_informations')]
    public function index(): Response
    {
        return $this->render('informations/index.html.twig', [
            'controller_name' => 'InformationsController',
        ]);
    }
}
