<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminEventsController extends AbstractController
{
    #[Route('/admin/events', name: 'app_admin_events')]
    public function index(): Response
    {
        return $this->render('admin_events/index.html.twig', [
            'controller_name' => 'AdminEventsController',
        ]);
    }
}
