<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    public function findCountQuestion($id): array
    {
        return $this->createQueryBuilder('u')
            ->select('count(q.id) as questionsNumber')
            ->leftjoin(Question::class, 'q', Join::WITH, 'q.author=u.id')
            ->where('u.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCountAnswer($id): array
    {
        return $this->createQueryBuilder('u')
            ->select('count(a.id) as answersNumber')
            ->leftjoin(Answer::class, 'a', Join::WITH, 'a.author=u.id')
            ->where('u.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
