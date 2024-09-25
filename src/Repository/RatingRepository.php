<?php

namespace App\Repository;

use App\Entity\Rating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rating>
 */
class RatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    //    /**
    //     * @return Rating[] Returns an array of Rating objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Rating
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

        public function getScores($user): array {
        // Create the query builder
        $query = $this->createQueryBuilder('r');
        

        $query->select('r.score, COUNT(r.id) as scoreCount')
        ->where('r.vendor = :user')
        ->setParameter('user', $user)
        ->groupBy('r.score')
        ->orderBy('r.score', 'ASC');

        $results = $query->getQuery()->getResult();

        $defaultScores = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        $averange = 0;
        $ratingCount = 0;

        foreach ($results as $result) {
            $defaultScores[$result['score']] = $result['scoreCount'];
            $averange += $result['score'] * $result['scoreCount'];
            $ratingCount += $result['scoreCount'];
        }
        if ($ratingCount > 0) {
            $averange /= $ratingCount;
        }

        $averange = (int) round($averange);

        return [$averange, $defaultScores];
    }

}
