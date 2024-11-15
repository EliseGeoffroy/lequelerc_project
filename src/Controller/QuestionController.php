<?php

namespace App\Controller;

use DateTime;
use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class QuestionController extends AbstractController
{
    #[Route('/question/ask', name: 'app_question_form')]
    public function askAQuestion(Request $request, EntityManagerInterface $em, AuthorRepository $repoAuthor): Response
    {

        $question = new Question();

        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);

        if (($form->isSubmitted()) && ($form->isValid())) {

            //Little hard-coding before authentication chapter
            $author = $repoAuthor->find(1);
            //End of hard-coding

            $question->setAuthor($author);
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
    public function likeAQuestion($score, Question $question, EntityManagerInterface $em)
    {
        $newRating = $question->getRating() + $score;
        $question->setRating($newRating);
        $em->flush();

        return $this->redirectToRoute('app_index');
    }
}
