<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findJoinAuthorCountAnswer(): array
    {

        return $this->createQueryBuilder('q')
            ->select(array('q.id', 'q.title', 'q.content', 'q.rating', 'q.createdAt', 'a.id as authorId', 'a.username', 'a.picture', 'a.color', 'count(an.id) as answersNumber'))
            ->innerjoin(User::class, 'a', JOIN::WITH, 'a.id=q.author')
            ->leftjoin(Answer::class, 'an', JOIN::WITH, 'an.question=q.id')
            ->groupBy('q.id')
            ->orderBy('q.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
