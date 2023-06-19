<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class NewsController extends AbstractController
{
    
    #[Route('/nouveautés', name: 'all_news')]
    public function index(NewsRepository $newsRepository): Response
    {
        $news = $newsRepository->findAll();
        return $this->render('news/index.html.twig', [
            'news' => $news
        ]);
    }
}
