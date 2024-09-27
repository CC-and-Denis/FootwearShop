<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

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

    public function findResearchedUser(String $research, int $qta, int $offset)
    {
        $query = $this->createQueryBuilder('u')
        ->where('u.username LIKE :search')
        ->setParameter('search', '%' . $research . '%');
        
        $productsAvaible=(clone $query)->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $query = $query
        ->addSelect(
            'CASE 
                WHEN u.username LIKE :exactStart THEN 1 
                WHEN u.username LIKE :contains THEN 2 
                ELSE 3 
            END AS HIDDEN relevance'
        )
        ->setParameter('exactStart', $research . '%')
        ->setParameter('contains', '%' . $research . '%')
        ->orderBy('relevance', 'ASC')
        ->addOrderBy('u.username', 'ASC')
        ->setMaxResults($qta)
        ->setFirstResult($offset)
        ->getQuery()
        ->getResult();

        return [$productsAvaible-$offset-$qta>0,$query];
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

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
