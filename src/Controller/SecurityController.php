<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class SecurityController extends AbstractController
{

    public function __construct(private FormLoginAuthenticator $authenticator) {}

    #[Route('/security/signUp', name: 'app_signUp')]
    public function signUp(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher, UserAuthenticatorInterface $userAuthenticator): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Bienvenue sur Lequelerc, le quelerc voyant! ;)');

            return $userAuthenticator->authenticateUser($user, $this->authenticator, $request);
        }

        return $this->render('security/signUp.html.twig', [
            'my_form' => $form->createView()
        ]);
    }


    #[Route('/security/signIn', name: 'app_signIn')]
    public function signIn(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        dump($error);
        $lastUserName = $authenticationUtils->getLastUsername();
        return $this->render('security/signIn.html.twig', [
            'last_username' => $lastUserName,
            'error' => $error
        ]);
    }


    #[Route('/security/signOut', name: 'app_signOut')]
    public function signOut(): Response
    {
        return $this->render('home/index.html.twig');
    }
}
