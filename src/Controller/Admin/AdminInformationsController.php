<?php

namespace App\Controller\Admin;

use App\Entity\Informations;
use App\Form\InformationsFormType;
use App\Repository\InformationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/informations', name: 'admin_informations_')]
class InformationsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(InformationsRepository $informationsRepository): Response
    {
        $infos = $informationsRepository->findBy([], ['id' => 'asc']);
        return $this->render('admin/informations/index.html.twig', compact('infos'));
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'ROLE_SECRETAIRE');

        $infos = new Informations();

        $infosForm = $this->createForm(InformationsFormType::class, $infos, ['attr' => ['class' => 'formEvent']]);

        // ON traite la requête

        $infosForm->handleRequest($request);

        //ON vérifie que le form est soumis et valide
        if($infosForm->isSubmitted() && $infosForm->isValid())
        {

            $InfosFile = $infosForm->get('picture')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($InfosFile) {
                $originalFilename = pathinfo($InfosFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                
                $newFilename = md5(uniqid()).'.'.$InfosFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $InfosFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $infos->setPicture($newFilename);
            }
            $em->persist($infos);
            $em->flush();

            $this->addFlash('success', 'l\'information a bien été ajouté');
            return $this->redirectToRoute('admin_informations_index');
        }

        return $this->render('admin/informations/add.html.twig', [
            'infosForm' => $infosForm->createView(),
            'infos' => $infos
        ]);
    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Informations $infos, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('INFORMATION_EDIT', $infos);
        
        $infosForm = $this->createForm(InformationsFormType::class, $infos, ['attr' => ['class' => 'formEvent']]);

        // ON traite la requête

        $infosForm->handleRequest($request);

        //ON vérifie que le form est soumis et valide
        if($infosForm->isSubmitted() && $infosForm->isValid())
        {
            $event = $infosForm->getData();
            $InfosFile = $infosForm->get('picture')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($InfosFile) {
                $originalFilename = pathinfo($InfosFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                
                $newFilename = md5(uniqid()).'.'.$InfosFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $InfosFile->move(
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
            $em->persist($infos);
            $em->flush();

            $this->addFlash('success', 'l\'informations a bien été modifié');
            return $this->redirectToRoute('admin_informations_index');
        }

        return $this->render('admin/informations/edit.html.twig', [
            'infosForm' =>  $infosForm->createView() 
        ]);
    }

    #[Route('/supression/{id}', name: 'delete')]
    public function delete(Informations $infos, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('INFORMATION_DELETE', $infos);

        $image = $infos->getPicture();

        if($image){
            $nomImage = $this->getParameter('images_directory') . '/' . $image;
            if(file_exists($nomImage)){
                unlink($nomImage);
            }
        }
        $em->remove($infos);
        $em->flush();

        $this->addFlash('success', 'l\'information a bien été supprimé');
        return $this->redirectToRoute('admin_informations_index');
        
    }
   
}
