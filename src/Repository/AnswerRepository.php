<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Answer;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    public function findJoinAuthor($idQuestion): array
    {
        return $this->createQueryBuilder('an')
            ->select(array('an.id', 'an.content', 'an.likes', 'an.dislikes', 'an.createdAt', 'au.username as authorUsername', 'au.color as authorColor', 'au.id as authorId'))
            ->innerJoin(User::class, 'au', Join::WITH, 'au.id=an.author')
            ->where('an.question=:idQuestion')
            ->orderBy('an.createdAt', 'DESC')
            ->setParameter(':idQuestion', $idQuestion)
            ->getQuery()
            ->getResult();
    }
}
