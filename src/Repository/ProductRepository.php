<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;

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

    public function findResearchedProduct(String $research, array $gender, array $age, array $types, array $brands, array $colors, array $sizes, int $qta, int $offset)
    {
        $query = $this->createQueryBuilder('p');

        $filters = [
            'gender' => $gender,
            'forKids' => $age,
            'type' => $types,
            'brand' => $brands,
            'color' => $colors,
            'size' => $sizes,
        ];

        $query
            ->where('p.model LIKE :research OR p.description LIKE :research')
            ->setParameter('research', '%' . $research . '%')
            ->orderBy('CASE 
                            WHEN p.model LIKE :research THEN 1
                            WHEN p.description LIKE :research THEN 2
                            ELSE 3
                        END', 'ASC');
// Iterate through the filters and apply them dynamically

        foreach ($filters as $field => $values) {
            if (!empty($values)) {
                $query
                    ->andWhere("p.$field IN (:$field)")
                    ->setParameter($field, $values);
            }
        }


        $productsAvaible=(clone $query)->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $query = $query
            ->setMaxResults($qta)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        return [$productsAvaible-$offset-$qta>0,$query];
    }

    public function findSimilarProduct(String $type, String $material, int $id, int $color, int $qta, int $offset)
    {
        $query = $this -> createQueryBuilder('p')
            ->where('p.type = :type')
            ->andWhere('p.material = :material')
            ->andWhere('p.id != :id')
            ->andWhere('p.color != :color')
            ->setParameter('type', $type)
            ->setParameter('material', $material)
            ->setParameter('id', $id)
            ->setParameter('color', $color);

        $productsAvaible=(clone $query)->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $query = $query
            ->setMaxResults($qta)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        return [$productsAvaible-$offset-$qta>0, $query];
    }

    public function getTotalPossibleFyProducts($pickedTypeNames, $pickedBrandNames, $pickedColorNames) {

        $query = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.type IN (:types)')
            ->andWhere('p.brand IN (:brands)')
            ->andWhere('p.color IN (:colors)')
            ->setParameter('types', $pickedTypeNames)
            ->setParameter('brands', $pickedBrandNames)
            ->setParameter('colors', $pickedColorNames);
        
        $queryResult = $query->getQuery()->getSingleScalarResult();
        return $queryResult;
    }
    

    public function fy_query(string $type, string $brand, string $color, int $offset) {

        $query = $this->createQueryBuilder('p')
            ->where('p.type = :type')
            ->andWhere('p.brand = :brand')
            ->andWhere('p.color = :color')
            ->setParameter('type', $type)
            ->setParameter('brand', $brand)
            ->setParameter('color', $color)

            ->orderBy('p.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults(1);
        
        $queryResult = $query->getQuery()->getOneOrNullResult();
        dump($queryResult);
        return $queryResult;

    }
}

?>