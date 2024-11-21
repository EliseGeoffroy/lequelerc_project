<?php

namespace App\Controller;

use DateTime;
use App\Entity\Question;
use App\Form\QuestionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class QuestionController extends AbstractController
{
    #[Route('/question/ask', name: 'app_question_form')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function askAQuestion(Request $request, EntityManagerInterface $em): Response
    {

        $question = new Question();

        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);

        if (($form->isSubmitted()) && ($form->isValid())) {

            $question->setAuthor($this->getUser());
            $question->setCreatedAt(new DateTime);

            $question->setRating(0);
            $em->persist($question);
            $em->flush();

            $this->addFlash('success', 'Votre question a bien été posée. Il ne reste plus qu\'à attendre les réponses');

            return $this->redirectToRoute('app_index');
        }


        return $this->render('question/ask.html.twig', [
            'my_form' => $form->createView()
        ]);
    }

    #[Route(
        '/question/{id}/{score}',
        name: 'app_question_rating',
        requirements: ['score' => '[-]{0,1}1', 'id' => '[\d]+']
    )]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function likeAQuestion($score, Question $question, EntityManagerInterface $em)
    {

        try {
            $this->denyAccessUnlessGranted('RATE', $question);
        } catch (AccessDeniedException $exception) {
            $this->addFlash('deny', 'Et non, petit coquin, tu ne peux pas voter pour ta propre question. Pas de narcissisme sur ce forum.');
            return $this->redirectToRoute('app_index');
        }
        $newRating = $question->getRating() + $score;
        $question->setRating($newRating);
        $em->flush();

        return $this->redirectToRoute('app_index');
    }
}
