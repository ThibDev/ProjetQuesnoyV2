<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use App\Repository\UsersRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/oubli-pass', name: 'app_forgot_password' )]
    public function forgotPassword(Request $request, UserRepository $usersRepository,
     TokenGeneratorInterface $tokenGeneratorInterface, EntityManagerInterface $entityManagerInterface,
     SendMailService $mail): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            // On va chercher l'utilisateur par son email
            $user = $usersRepository->findOneByEmail($form->get('email')->getData());

            //On vérifie si on a un utilisateur
            if($user){
                // On génère un token de réinitialisation
                $token = $tokenGeneratorInterface->generateToken();
                $user->setResetToken($token);
                $entityManagerInterface->persist($user);
                $entityManagerInterface->flush();

                // On génère un lien de réinitialisation
                $url = $this->generateUrl('app_reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                // On créer les données du mail
                $context = compact('url', 'user');

                //On envoie le mail
                $mail->send(
                    'no-reply@lequesnoy.fr',
                    $user->getEmail(),
                    'Réinitialisation du mot de passe',
                    'password_reset',
                    $context
                );

                $this->addFlash('success', 'l\'email de réinitialisation a bien été envoyé');
                return $this->redirectToRoute('app_login');

            }
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'RequestPassForm' => $form->createView()
        ]);
    }

    #[Route('/oubli-pass/{token}', name:'app_reset_pass')]
    public function resetPass(string $token, Request $request, UserRepository $usersRepository,
    EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $passwordHasher): Response
    {
        // On vérifie si on a ce token dans la base de données
        $user = $usersRepository->findOneByResetToken($token);

        if($user){
            $form = $this->createForm(ResetPasswordFormType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) { 
                // On efface le token
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                    );
                    $entityManagerInterface->persist($user);
                    $entityManagerInterface->flush();

                    $this->addFlash('success', 'Votre mot de passe a bien été modifier');
                    return $this->redirectToRoute('app_login');
            }
    

            return $this->render('security/reset_password.html.twig',[
                'passForm' => $form->createView()
            ]);
        }
        $this->addFlash('danger', 'jeton invalide');
        return $this->redirectToRoute('app_login');
    }
}
