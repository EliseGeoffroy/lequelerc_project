<?php

namespace App\Controller;

use DateTime;
use App\Entity\Answer;
use App\Form\AnswerType;
use App\Repository\AuthorRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReadController extends AbstractController
{
    #[Route(
        path: '/read/{idQuestion}',
        name: 'app_read',
        requirements: ['idQuestion' => '[\d]+']
    )]
    public function index($idQuestion, QuestionRepository $repoQuestion, EntityManagerInterface $em, AuthorRepository $repoAuthor, Request $request): Response
    {

        $newAnswer = new Answer();
        $question = $repoQuestion->find($idQuestion);

        $answersTable = $question->getAnswers();

        $form = $this->createForm(AnswerType::class, $newAnswer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Little hardcoding before authentication chapter
            $author = $repoAuthor->find(2);
            //

            $newAnswer->setLikes(0);
            $newAnswer->setDisLikes(0);
            $newAnswer->setAuthor($author);
            $newAnswer->setQuestion($question);
            $newAnswer->setCreatedAt(new DateTime);
            $em->persist($newAnswer);
            $em->flush();

            $newAnswer = new Answer();
            $form = $this->createForm(AnswerType::class, $newAnswer);

            $this->addFlash('success', 'Merci pour votre réponse.');
        };
        return $this->render('read/index.html.twig', [
            'controller_name' => 'ReadController',
            'question' => $question,
            'answers' => $answersTable,
            'my_form' => $form->createView()
        ]);
    }



    #[Route(
        '/answer/{id}/{score}',
        name: 'app_answer_rating',
        requirements: ['score' => '[-]{0,1}1', 'id' => '[\d]+']
    )]
    public function likeAQuestion(Request $request, $score,  Answer $answer, EntityManagerInterface $em)
    {

        if ($score == 1) {
            $newLikes = $answer->getLikes() + 1;
            $answer->setLikes($newLikes);
        } else {
            $newDislikes = $answer->getDislikes() + 1;
            $answer->setDislikes($newDislikes);
        }
        $em->flush();

        $referer = $request->headers->get('referer');
        return $referer ? $this->redirect($referer) : $this->redirectToRoute('home');
    }
}
