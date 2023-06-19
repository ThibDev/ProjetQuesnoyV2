<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Form\NewsFormType;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/nouveaute', name: 'admin_news_')]
class NewsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(NewsRepository $newsRepository): Response
    {
        $news = $newsRepository->findBy([], ['id' => 'asc']);
        return $this->render('admin/news/index.html.twig', compact('news'));
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'ROLE_SECRETAIRE');

        $news = new News();

        $newsForm = $this->createForm(NewsFormType::class, $news, ['attr' => ['class' => 'formNews']]);

        $newsForm->handleRequest($request);

        if($newsForm->isSubmitted() && $newsForm->isValid())
        {

            $NewsFile = $newsForm->get('picture')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($NewsFile) {
                $originalFilename = pathinfo($NewsFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                
                $newFilename = md5(uniqid()).'.'.$NewsFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $NewsFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $news->setPicture($newFilename);
            }
            $em->persist($news);
            $em->flush();

            $this->addFlash('success', 'l\'actualité a bien été ajouté');
            return $this->redirectToRoute('admin_news_index');
        }

        
        return $this->render('admin/news/add.html.twig', [
            'newsForm' => $newsForm->createView(),
            'news' => $news
        ]);
    }


    #[Route('/edition/{id}', name: 'edit')]
    public function edit(News $news, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('NEW_EDIT', $news);
        
        $newsForm = $this->createForm(NewsFormType::class, $news, ['attr' => ['class' => 'formEvent']]);

        // ON traite la requête

        $newsForm->handleRequest($request);

        //ON vérifie que le form est soumis et valide
        if($newsForm->isSubmitted() && $newsForm->isValid())
        {
            $news = $newsForm->getData();
            $NewsFile = $newsForm->get('picture')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($NewsFile) {
                $originalFilename = pathinfo($NewsFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                
                $newFilename = md5(uniqid()).'.'.$NewsFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $NewsFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $news->setPicture($newFilename);
            }
            $em->persist($news);
            $em->flush();

            $this->addFlash('success', 'l\'actualité a bien été modifié');
            return $this->redirectToRoute('admin_news_index');
        }

        return $this->render('admin/news/edit.html.twig', [
            'newsForm' =>  $newsForm->createView(),
            'news' => $news
        ]);
    }


    #[Route('/supression/{id}', name: 'delete')]
    public function delete(News $news, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('news_DELETE', $news);

        $image = $news->getPicture();

        if($image){
            $nomImage = $this->getParameter('images_directory') . '/' . $image;
            if(file_exists($nomImage)){
                unlink($nomImage);
            }
        }
        $em->remove($news);
        $em->flush();

        $this->addFlash('success', 'l\'actualité a bien été supprimé');
        return $this->redirectToRoute('admin_news_index');
        
    }
}