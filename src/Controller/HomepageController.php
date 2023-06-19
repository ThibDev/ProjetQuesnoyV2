<?php

namespace App\Controller;


use App\Repository\EventsRepository;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(EventsRepository $eventsRepository, NewsRepository $newsRepository): Response
    {
        $news = $newsRepository->findBy(array(), array('id' => 'desc'), 3);
        $events = $eventsRepository->findAll();
        return $this->render('main/index.html.twig', [
            'events' => $events,
            'news' => $news
        ]);
    }
}
