<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/Associations', name: 'associations_')]
class AssociationsController extends AbstractController{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('associations/index.html.twig');
    }
}