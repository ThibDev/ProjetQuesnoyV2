<?php

namespace App\Controller;

use App\Repository\InformationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/Municipalité', name: 'municipalite_')]
class MunicipaliteController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('municipalite/index.html.twig');
    }
    #[Route('/maire', name: 'maire')]
    public function Maire(): Response
    {
        return $this->render('municipalité/maire.html.twig');
    }
    #[Route('/elus', name: 'elus')]
    public function elus(): Response
    {
        return $this->render('municipalité/elus.html.twig');
    }
    #[Route('/mairie', name: 'mairie')]
    public function mairie(): Response
    {
        return $this->render('municipalité/mairie.html.twig');
    }
    #[Route('/salledesfetes', name: 'salledesfetes')]
    public function salledesfetes(): Response
    {
        return $this->render('municipalité/salledesfetes.html.twig');
    }
    #[Route('/informations', name: 'informations')]
    public function informations(InformationsRepository $informationsRepository): Response
    {
        $infos = $informationsRepository->findBy([], ['id' => 'asc']);
        return $this->render('informations/index.html.twig', compact('infos'));
    }
}