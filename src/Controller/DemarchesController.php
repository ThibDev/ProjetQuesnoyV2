<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/Demarches', name: 'demarches_')]
class DemarchesController extends AbstractController{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('demarches/index.html.twig');
    }
    #[Route('/carte_identite', name: 'carte_identite')]
    public function identite(): Response
    {
        return $this->render('demarches/carte_identite.html.twig');
    }
    #[Route('/carte_grise', name: 'carte_grise')]
    public function carte_grise(): Response
    {
        return $this->render('demarches/carte_grise.html.twig');
    }
    #[Route('/carte_electorale', name: 'carte_electorale')]
    public function carte_electorale(): Response
    {
        return $this->render('demarches/carte_electorale.html.twig');
    }
    #[Route('/naissance', name: 'naissance')]
    public function naissance(): Response
    {
        return $this->render('demarches/naissance.html.twig');
    }
    #[Route('/mariage', name: 'mariage')]
    public function mariage(): Response
    {
        return $this->render('demarches/mariage.html.twig');
    }
    #[Route('/passeport', name: 'passeport')]
    public function passeport(): Response
    {
        return $this->render('demarches/passeport.html.twig');
    }
    #[Route('/recenssement', name: 'recenssement')]
    public function recenssement(): Response
    {
        return $this->render('demarches/recenssement.html.twig');
    }
    #[Route('/deces', name: 'deces')]
    public function deces(): Response
    {
        return $this->render('demarches/deces.html.twig');
    }
    #[Route('/conservation', name: 'conservation')]
    public function conservation(): Response
    {
        return $this->render('demarches/conservation.html.twig');
    }
}