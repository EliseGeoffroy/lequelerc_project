<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route(
        '/profile/{id}',
        name: 'app_profile',
        requirements: ['id' => '[\d]+']
    )]
    public function index(UserRepository $repoUser, User $user, $id): Response
    {
        $stats = $this->countAnswerQuestion($id, $repoUser);

        return $this->render(
            'profile/profile.html.twig',
            [
                'author' => $user,
                'stats' => $stats
            ]
        );
    }

    #[Route(
        '/profile/questions/{id}',
        name: 'app_profile_questions',
        requirements: ['id' => '[\d]+']
    )]
    public function question(QuestionRepository $repoQuestion, UserRepository $repoUser, User $user, $id): Response
    {
        $questions = $repoQuestion->findBy(['author' => $user]);

        $stats = $this->countAnswerQuestion($id, $repoUser);

        return $this->render(
            'profile/profile.html.twig',
            [
                'questions' => $questions,
                'author' => $user,
                'stats' => $stats
            ]
        );
    }

    #[Route(
        '/profile/answers/{id}',
        name: 'app_profile_answers',
        requirements: ['id' => '[\d]+']
    )]
    public function answer(AnswerRepository $repoAnswer, UserRepository $repoUser, User $user, $id): Response
    {
        $answers = $repoAnswer->findBy(['author' => $user]);
        $stats = $this->countAnswerQuestion($id, $repoUser);

        return $this->render(
            'profile/profile.html.twig',
            [
                'answers' => $answers,
                'author' => $user,
                'stats' => $stats
            ]
        );
    }

    #[Route(
        '/profile/edit/{id}',
        name: 'app_profile_edit',
        requirements: ['id' => '[\d]+']
    )]
    public function edit(Request $request, User $user, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

            $em->persist($user);
            $em->flush();

            return  $this->redirectToRoute('app_profile', ['id' => $user->getId()]);
        }

        return $this->render(
            'profile/edit.html.twig',
            [
                'author' => $user,
                'my_form' => $form
            ]
        );
    }

    private function countAnswerQuestion($id, UserRepository $repoUser)
    {

        $questionsNumber = $repoUser->findCountQuestion($id)[0];
        $answersNumber = $repoUser->findCountAnswer($id)[0];
        $stats = [...$questionsNumber, ...$answersNumber];

        return $stats;
    }
}
