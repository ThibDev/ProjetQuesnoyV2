<?php 

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/admin/utilisateurs', name: 'admin_utilisateurs_')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UsersRepository $usersRepository): Response
    {
        $users = $usersRepository->findBy([], ['id' => 'asc']);
        return $this->render('admin/users/index.html.twig', compact('users'));
    }
   
    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Users $user, Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator,
    EntityManagerInterface $em, SendMailService $mail, JWTService $jwt): Response
    {
        $this->denyAccessUnlessGranted('USER_EDIT', $user);
       
        $form = $this->createForm(RegistrationFormType::class, $user, ['attr' => ['class' => 'formregister']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user = $form->getData();
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $em->persist($user);
            $em->flush();
            // do anything else you need here, like send an email

            // On génère le token:
            // On créer le Header

            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // On créer le Payload

            $payload = [
                'user_id' => $user->getId()
            ];

            // On génère le token

            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));


            // On envoie le mail

            $mail->send(
                'no-reply@lequesnoy.net',
                $user->getEmail(),
                'Activation de votre compte sur le site Le Quesnoy en Artois',
                'register',
                compact('user','token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('/admin/users/edit.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/supression/{id}', name: 'delete')]
    public function delete(Users $users, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('USER_DELETE', $users);
        $em->remove($users);
        $em->flush();

        $this->addFlash('success', 'l\'utilisateur a bien été supprimé');
        return $this->redirectToRoute('admin_utilisateurs_index');
        
    }
}