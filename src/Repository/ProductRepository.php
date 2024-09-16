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
        // Create the query to fetch products sorted by views and sales
        return $this->createQueryBuilder('p')
            ->orderBy('p.views', 'DESC')
            //->addOrderBy('p.items_sold', 'DESC')
            ->setFirstResult($position) 
            ->setMaxResults($qta)      
            ->getQuery()
            ->getResult();
    }
    public function findResearchedProduct(String $research, array $gender, array $age, int $qta, int $offset)
    {
        return $this -> createQueryBuilder('p')
            ->where('p.description LIKE :research')
            ->andWhere('p.gender IN (:gender)')
            ->andWhere('p.forKids IN (:age)')
            ->setParameter('research', '%' . $research . '%')
            ->setParameter('gender', $gender)
            ->setParameter('age', $age)
            ->setMaxResults($qta)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
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