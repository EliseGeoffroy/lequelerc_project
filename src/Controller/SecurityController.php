<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\UserType;
use App\Entity\UserForgetPassword;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Form\UserForgetPasswordType;
use App\Repository\UserForgetPasswordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

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

    #[Route('/forgetPassword', name: 'app_password_forgotten')]
    public function forgetPassword(EntityManagerInterface $em, Request $request,  UserRepository $repo,): Response
    {

        $userForgetPassword = new UserForgetPassword();

        $form = $this->createForm(UserForgetPasswordType::class, $userForgetPassword);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userIdentified = $repo->findBy(['email' => $userForgetPassword->getEmail()]);


            if ($userIdentified) {
                $userForgetPassword->setUser($userIdentified[0]);
                $userForgetPassword->setExpirationDate(new \DateTimeImmutable('+2 hours'));
                $userForgetPassword->setToken(substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(30))), 0, 20));
                $em->persist($userForgetPassword);
                $em->flush();

                $this->addFlash('success', "Un email va vous être envoyé pour réinitialiser votre mot de passe.");
                return $this->redirectToRoute('app_index');
            } else {
                $this->addFlash('none', "Cet email est inconnu. Veuillez vous inscrire.");
            }
        }

        return $this->render('security/forgetPWD.html.twig', [
            'my_form' => $form->createView()
        ]);
    }

    #[Route('/resetPWD/{token}/{email}', name: 'app_reset_password')]
    public function resetPassword($token, $email, UserForgetPasswordRepository $repoForget, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {

        $userForgetPassword = $repoForget->findOneBy(['token' => $token, 'email' => $email]);

        if (($userForgetPassword) && ($userForgetPassword->getExpirationDate() > new \DateTime('now'))) {

            $form = $this->createForm(ResetPasswordType::class);
            $form->handleRequest($request);
        } else {
            $this->addFlash('fail', "Le lien n'est pas bon. Veuillez recommencer les manipulations dès le début.");
            return $this->redirectToRoute('app_index');
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $newPassword = $form->get('password')->getData();

            $user = $userForgetPassword->getUser();
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $em->remove($userForgetPassword);

            $em->flush();
            $this->addFlash('success', "Votre mot de passe a bien été modifié!");
            return $this->redirectToRoute('app_signIn');
        }
        return $this->render('security/resetPWD.html.twig', [
            'my_form' => $form->createView()
        ]);
    }
}
