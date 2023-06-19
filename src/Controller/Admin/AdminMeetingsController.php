<?php

namespace App\Controller\Admin;

use App\Entity\Meetings;
use App\Form\MeetingsFormType;
use App\Repository\MeetingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/reunions', name: 'admin_meetings_')]
class MeetingsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(MeetingsRepository $meetingsRepository): Response
    {
        $meetings = $meetingsRepository->findBy([], ['id' => 'asc']);
        return $this->render('admin/meetings/index.html.twig', compact('meetings'));
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'ROLE_SECRETAIRE');

        $meetings = new Meetings();

        $meetingsForm = $this->createForm(MeetingsFormType::class, $meetings, ['attr' => ['class' => 'formNews']]);

        // ON traite la requête

        $meetingsForm->handleRequest($request);

        //ON vérifie que le form est soumis et valide
        if($meetingsForm->isSubmitted() && $meetingsForm->isValid())
        {

            $em->persist($meetings);
            $em->flush();

            $this->addFlash('success', 'la réunion a bien été ajouté');
            return $this->redirectToRoute('admin_meetings_index');
        }

        return $this->render('admin/meetings/add.html.twig', [
            'meetingsForm' => $meetingsForm->createView(),
            'meetings' => $meetings
        ]);
    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Meetings $meetings, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'ROLE_SECRETAIRE');
        
        $meetingsForm = $this->createForm(MeetingsFormType::class, $meetings, ['attr' => ['class' => 'formNews']]);

        // ON traite la requête

        $meetingsForm->handleRequest($request);

        //ON vérifie que le form est soumis et valide
        if($meetingsForm->isSubmitted() && $meetingsForm->isValid())
        {
            $meetings = $meetingsForm->getData();
            
            $em->persist($meetings);
            $em->flush();

            $this->addFlash('success', 'la réunion a bien été modifié');
            return $this->redirectToRoute('admin_meetings_index');
        }

        return $this->render('admin/meetings/edit.html.twig', [
            'meetingsForm' =>  $meetingsForm->createView() 
        ]);
    }
    
    #[Route('/supression/{id}', name: 'delete')]
    public function delete(Meetings $meetings, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'ROLE_SECRETAIRE');
        $em->remove($meetings);
        $em->flush();

        $this->addFlash('success', 'la réunion a bien été supprimé');
        return $this->redirectToRoute('admin_meetings_index');
        
    }
}
