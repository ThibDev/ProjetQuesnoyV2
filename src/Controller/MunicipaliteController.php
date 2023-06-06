<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MunicipaliteController extends AbstractController
{
    #[Route('/municipalite', name: 'app_municipalite')]
    public function index(): Response
    {
        return $this->render('municipalite/index.html.twig', [
            'controller_name' => 'MunicipaliteController',
        ]);
    }
}
