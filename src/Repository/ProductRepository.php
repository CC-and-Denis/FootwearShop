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
        $query=$this->createQueryBuilder('product')
            //->addOrderBy('p.items_sold', 'DESC')
            ->orderBy('product.views', 'DESC');


        $productsAvaible=(clone $query)->select('count(product.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $productsToLoad=$query
            ->setFirstResult($position)
            ->setMaxResults($qta) 
            ->getQuery()
            ->getResult();

        $result=[$productsAvaible-$position-$qta>0,$productsToLoad];
        return $result;
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

?>