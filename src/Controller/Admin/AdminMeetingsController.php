<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminMeetingsController extends AbstractController
{
    #[Route('/admin/meetings', name: 'app_admin_meetings')]
    public function index(): Response
    {
        return $this->render('admin_meetings/index.html.twig', [
            'controller_name' => 'AdminMeetingsController',
        ]);
    }
}
