<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DemarchesController extends AbstractController
{
    #[Route('/demarches', name: 'app_demarches')]
    public function index(): Response
    {
        return $this->render('demarches/index.html.twig', [
            'controller_name' => 'DemarchesController',
        ]);
    }
}
