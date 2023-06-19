<?php

namespace App\Controller\Admin;

use App\Entity\Events;
use App\Form\EventsFormType;
use App\Repository\EventsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/evenements', name: 'admin_events_')]
class EventsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EventsRepository $eventsRepository): Response
    {
        $events = $eventsRepository->findBy([], ['id' => 'asc']);
        return $this->render('admin/events/index.html.twig', compact('events'));
    }
    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'ROLE_SECRETAIRE');

        $event = new Events();

        $eventsForm = $this->createForm(EventsFormType::class, $event);

        // ON traite la requête

        $eventsForm->handleRequest($request);

        //ON vérifie que le form est soumis et valide
        if($eventsForm->isSubmitted() && $eventsForm->isValid())
        {

            $EventFile = $eventsForm->get('picture')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($EventFile) {
                $originalFilename = pathinfo($EventFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                
                $newFilename = md5(uniqid()).'.'.$EventFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $EventFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $event->setPicture($newFilename);
            }
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'l\'évènement a bien été ajouté');
            return $this->redirectToRoute('admin_events_index');
        }

        return $this->render('admin/events/add.html.twig', [
            'eventsForm' => $eventsForm->createView(),
            'events' => $event
        ]);
    }
    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Events $event, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('EVENT_EDIT', $event);
        
        $eventsForm = $this->createForm(EventsFormType::class, $event, ['attr' => ['class' => 'formEvent']]);

        // ON traite la requête

        $eventsForm->handleRequest($request);

        //ON vérifie que le form est soumis et valide
        if($eventsForm->isSubmitted() && $eventsForm->isValid())
        {
            $event = $eventsForm->getData();
            $EventFile = $eventsForm->get('picture')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($EventFile) {
                $originalFilename = pathinfo($EventFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                
                $newFilename = md5(uniqid()).'.'.$EventFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $EventFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $event->setPicture($newFilename);
            }
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'l\'évènement a bien été modifié');
            return $this->redirectToRoute('admin_events_index');
        }

        return $this->render('admin/events/edit.html.twig', [
            'eventsForm' =>  $eventsForm->createView() 
        ]);
    }
    #[Route('/supression/{id}', name: 'delete')]
    public function delete(Events $event, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('EVENT_DELETE', $event);

        $image = $event->getPicture();

        if($image){
            $nomImage = $this->getParameter('images_directory') . '/' . $image;
            if(file_exists($nomImage)){
                unlink($nomImage);
            }
        }
        $em->remove($event);
        $em->flush();

        $this->addFlash('success', 'l\'évènement a bien été supprimé');
        return $this->redirectToRoute('admin_events_index');
        
    }
}
