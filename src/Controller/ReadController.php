<?php

namespace App\Controller;

use DateTime;
use App\Entity\Answer;
use App\Form\AnswerType;
use App\Repository\AnswerRepository;
use App\Repository\AuthorRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ReadController extends AbstractController
{
    #[Route(
        path: '/read/{idQuestion}',
        name: 'app_read',
        requirements: ['idQuestion' => '[\d]+']
    )]
    public function index($idQuestion, QuestionRepository $repoQuestion, EntityManagerInterface $em, Request $request, AnswerRepository $repoAnswer): Response
    {

        $newAnswer = new Answer();
        $question = $repoQuestion->find($idQuestion);

        $answersTable = $repoAnswer->findJoinAuthor($idQuestion);


        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $form = $this->createForm(AnswerType::class, $newAnswer);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $newAnswer->setLikes(0);
                $newAnswer->setDisLikes(0);
                $newAnswer->setAuthor($this->getUser());
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
        } else {
            return $this->render('read/index.html.twig', [
                'controller_name' => 'ReadController',
                'question' => $question,
                'answers' => $answersTable,
            ]);
        }
    }



    #[Route(
        '/answer/{id}/{score}',
        name: 'app_answer_rating',
        requirements: ['score' => '[-]{0,1}1', 'id' => '[\d]+']
    )]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function likeAQuestion(Request $request, $score,  Answer $answer, EntityManagerInterface $em)
    {

        try {
            $this->denyAccessUnlessGranted('RATE', $answer);
        } catch (AccessDeniedException $exception) {
            if (($this->getUser() == $answer->getAuthor())) {
                $this->addFlash('deny', 'Et non, petit coquin, tu ne peux pas voter pour ta propre question. Pas de narcissisme sur ce forum.');
            } else {
                $this->addFlash('deny', 'On ne vote qu\'une seule fois, c\'est la démocratie ici!');
            }
            $referer = $request->headers->get('referer');
            return $referer ? $this->redirect($referer) : $this->redirectToRoute('home');
        }

        if ($score == 1) {
            $newLikes = $answer->getLikes() + 1;
            $answer->setLikes($newLikes);
        } else {
            $newDislikes = $answer->getDislikes() + 1;
            $answer->setDislikes($newDislikes);
        }
        $em->flush();
        $answer->addVoter($this->getUser());

        $referer = $request->headers->get('referer');
        return $referer ? $this->redirect($referer) : $this->redirectToRoute('home');
    }
}
