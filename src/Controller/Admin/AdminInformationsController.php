<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminInformationsController extends AbstractController
{
    #[Route('/admin/informations', name: 'app_admin_informations')]
    public function index(): Response
    {
        return $this->render('admin_informations/index.html.twig', [
            'controller_name' => 'AdminInformationsController',
        ]);
    }
}
