<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminHomepageController extends AbstractController
{
    #[Route('/admin/homepage', name: 'app_admin_homepage')]
    public function index(): Response
    {
        return $this->render('admin_homepage/index.html.twig', [
            'controller_name' => 'AdminHomepageController',
        ]);
    }
}
