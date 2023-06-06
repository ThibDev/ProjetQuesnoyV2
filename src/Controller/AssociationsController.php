<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssociationsController extends AbstractController
{
    #[Route('/associations', name: 'app_associations')]
    public function index(): Response
    {
        return $this->render('associations/index.html.twig', [
            'controller_name' => 'AssociationsController',
        ]);
    }
}
