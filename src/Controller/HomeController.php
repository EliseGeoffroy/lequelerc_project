<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(QuestionRepository $repo): Response
    {
        $questionsTable = $repo->findJoinAuthorCountAnswer();


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'questions' => $questionsTable
        ]);
    }

    #[Route('/question', name: 'app_question')]
    public function question(): Response
    {
        return $this->render('home/question.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/myquestions', name: 'app_myquestions')]
    public function myQuestions(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/myanswers', name: 'app_myanswers')]
    public function myAnswers(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
