<?php

namespace App\Controller;

use App\Entity\Events;
use App\Repository\EventsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class EventsController extends AbstractController
{
    #[Route('/évènements', name: 'all_events')]
    public function index(EventsRepository $eventsRepository): Response
    {
        $events = $eventsRepository->findAll();
        return $this->render('events/index.html.twig', [
            'events' => $events
        ]);
    }
}
