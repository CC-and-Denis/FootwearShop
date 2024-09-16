<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findPopularProducts(int $qta, int $position): array
    {
        $qb=$this->createQueryBuilder('p')
            ->orderBy('p.views', 'DESC')
            //->addOrderBy('p.items_sold', 'DESC')
            ->setFirstResult($position);

        $l=(clone $qb)->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
        

        $results=$qb->setMaxResults($qta)      
           ->getQuery()
           ->getResult();

        $lista=[$l-$position-$qta>0,$results];
        return $lista;
    }
    public function findFavouriteProduct(String $type, String $brand, String $color, int $offset)
    {
        return $this -> createQueryBuilder('p')
            ->where('p.type = :type')
            ->andWhere('p.brand = :brand')
            ->andWhere('p.color = :color')
            ->setParameter('type', $type)
            ->setParameter('brand', $brand)
            ->setParameter('color', $color)
            ->setMaxResults(1)
            ->setFirstResult($offset)
            ->getQuery()
            ->getOneOrNullResult();
    }
}