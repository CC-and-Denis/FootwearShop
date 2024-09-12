<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Cookie;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ApiController extends AbstractController {

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, Filesystem $filesystem)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/getProductByPopular/{qta}-{position}', name: 'get_product_by_popular', methods: ['GET'])]
    public function getProductByPopular(int $qta, int $position, ProductRepository $productRepository): Response
    {
        // Fetch the popular products from the repository
        $products = $productRepository->findPopularProducts($qta, $position);
        $hasMore = count($products) == $qta;

        return $this->render('product/product_card_component.html.twig',[
            "products"=>$products,
            'hasMore' => $hasMore,
        ]);
    }

    #[Route('/api/getProductByResearch', name:'get_product_by_research')]
    public function getProductByResearch(int $qta, int $position, array $data, ProductRepository $productRepository) :Response{
        $products = $productRepository -> get_product_by_research();
        $hasMore = count($products) == $qta;

        return $this->render('product/product_card_component.html.twig',[
            "products"=>$products,
            'hasMore' => $hasMore,
        ]);
    }

    #[Route('/api/fyp-function/{qta}-{position}',)]
    public function fyp_function($qta, $position, ProductRepository $productRepository, Request $request): Response 
    { 
        $typeCookie = json_decode($request->cookies->get('type'), true);
        $brandCookie = json_decode($request->cookies->get('brand'), true);
        $colorCookie = json_decode($request->cookies->get('color'), true);

        arsort($typeCookie);  // Sorting the type array
        arsort($brandCookie); // Sorting the brand array
        arsort($colorCookie); // Sorting the color array
    
        if (empty($typeCookie) || empty($brandCookie) || empty($colorCookie)) {
            return $this->render('homePage.html.twig',);
        }
    
        // Calculate items based on the provided conditions
        $items = $this -> calculateItems($typeCookie, $brandCookie, $colorCookie, $position, $qta, $productRepository);

        $hasMore = count($items ) == $qta;

        return $this->render('product/product_card_component.html.twig',[
            "products"=>$items,
            'hasMore' => $hasMore,  
        ]);
    }








    private function getDivisionRate($first, $second) {
        if ($second == 0){
            if ($first == 0)
                return 1;
            else
                return intval($first / 1);
        }
        if ($first / $second > 10) return -1;
        return intval($first / $second);
    }
    
    public function calculatePriorities($array) {
        for ($i=0; $i<count($array)-1; $i++) {
            for ($j = $i+1; $j<count($array); $j++) {
                $array[$i] *= $array[$j];
            }
        }
        return $array;
    }


    // Function to calculate and simulate the for loops described
    private function calculateItems($types, $brands, $colors, $startingIndex, $productCount, $productRepository) {

        // Step 1: Work with the type relations
        $typeDivisions = [];
        $typeNames = array_keys($types);
        $typeValues = array_values($types);
        $totalTypeItems = 0;
        $totalTypeRate = 0;

        $pickedTypeNames = [];
    
        for ($i = 1; $i < count($typeValues); $i++) {
            $divResult = $this -> getDivisionRate($typeValues[$i - 1], $typeValues[$i]);
            if ($divResult == -1 || $totalTypeRate >= 15 || $totalTypeItems >= 2){
                $typeDivisions[] = 1;
                $pickedTypeNames[] = $typeNames[$i - 1];
                break;
            }
            $typeDivisions[] = $divResult;
            $pickedTypeNames[] = $typeNames[$i - 1];
            $totalTypeRate += $divResult;
            $totalTypeItems ++;
        }
    
        // Step 2: Work with brands (same logic as types, stop at 10 items)
        $brandDivisions = [];
        $brandNames = array_keys($brands);
        $brandValues = array_values($brands);
        $totalBrandItems = 0;
        $totalBrandRate = 0;

        $pickedBrandNames = [];
    
        for ($i = 1; $i < count($brandValues); $i++) {
            $divResult = $this -> getDivisionRate($brandValues[$i - 1], $brandValues[$i]);
            if ($divResult == -1 || $totalBrandRate >= 10 || $totalBrandItems >= 2) {
                $brandDivisions[] = 1;
                $pickedBrandNames[] = $brandNames[$i - 1];
                break;
            }
            $brandDivisions[] = $divResult;
            $pickedBrandNames[] = $brandNames[$i - 1];
            $totalBrandRate += $divResult;
            $totalBrandItems ++;
        }
    
        // Step 3: Work with colors (same logic as types and brands)
        $colorDivisions = [];
        $colorNames = array_keys($colors);
        $colorValues = array_values($colors);
        $totalColorItems = 0;
        $totalColorRate = 0;
        $pickedColorNames = [];
        for ($i = 1; $i < count($colorValues); $i++) {
            $divResult = $this -> getDivisionRate($colorValues[$i - 1], $colorValues[$i]);
            if ($divResult == -1 || $totalColorRate >= 10 || $totalColorItems >= 1) {
                $colorDivisions[] = 1;
                $pickedColorNames[] = $colorNames[$i - 1];
                break;
            }
            $colorDivisions[] = $divResult;
            $pickedColorNames[] = $colorNames[$i - 1];
            $totalColorRate += $divResult;
            $totalColorItems ++;
        }

        $typeDivisions = $this->calculatePriorities($typeDivisions);
        $brandDivisions = $this->calculatePriorities($brandDivisions);
        $colorDivisions = $this->calculatePriorities($colorDivisions);

        dump("typeDivisions: ", $types, $typeDivisions);
        dump("brandDivisions: ", $brands, $brandDivisions);
        dump("colorDivisions: ", $colors, $colorDivisions);
        dump($pickedTypeNames, $pickedBrandNames, $pickedColorNames);
    
        $typeList = ['type1', 'type2', 'type3'];  // Example types
        $brand = 'some_brand';
        $color = 'some_color';

        $query = $this->entityManager->createQuery(
            'SELECT COUNT(p.id) 
            FROM App\Entity\Product p
            WHERE p.type IN (:types) 
            AND p.brand IN (:brand) 
            AND p.color IN (:color)'
        );

        $query->setParameter('types', $pickedTypeNames);
        $query->setParameter('brand', $pickedBrandNames);
        $query->setParameter('color', $pickedColorNames);

        $totalRecords = $query->getSingleScalarResult() - $startingIndex;

        dump('totalRecords', $totalRecords);

        //$this->entityManager->getConnection()->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        // Step 4: Use loops based on the divisions found
        $results = [];
        $combinationCounter = []; // To store occurrences of each combination
        $counter = 0;
    
        //for ($i = 0; $i < $productCount; $i++) {
        while (true) {
            // Check type
            foreach ($typeDivisions as $typeIndex => $typeLoopCount) {
                for ($j = 0; $j < $typeLoopCount; $j++) {
                    // Check brand
                    foreach ($brandDivisions as $brandIndex => $brandLoopCount) {
                        for ($k = 0; $k < $brandLoopCount; $k++) {
                            // Check color
                            foreach ($colorDivisions as $colorIndex => $colorLoopCount) {
                                for ($l = 0; $l < $colorLoopCount; $l++) {
                                    $type = $typeNames[$typeIndex];
                                    $brand = $brandNames[$brandIndex];
                                    $color = $colorNames[$colorIndex];
                                    // Create a combination key to track how many times it has occurred
                                    $combinationKey = $type . '-' . $brand . '-' . $color;
                                    
                                    // Initialize counter for this combination if not set
                                    if (!isset($combinationCounter[$combinationKey])) {
                                        $combinationCounter[$combinationKey] = 0; //null offset is 1
                                    }
                                    dump($type, $brand, $color, $combinationCounter[$combinationKey]);
                                    dump("combinationCounter", $combinationCounter);
                                    if ($combinationCounter[$combinationKey] != -1) {

                                            $queryResult = $this -> fyp_query($type, $brand, $color, $combinationCounter[$combinationKey]);

                                            if ($queryResult === null) {
                                                $combinationCounter[$combinationKey] = -1;
                                            }
                                            else{

                                                dump("productCount", $productCount);
                                                
                                                if ($startingIndex == 0 && $productCount >= 1) {

                                                    dump("query-ok", $type, $brand, $color, $combinationCounter[$combinationKey]);
                                                    $results[] = $queryResult;
                                                    $counter ++;
                                                    /*
                                                    $results[] = [
                                                        'type' => $type,
                                                        'brand' => $brand,
                                                        'color' => $color,
                                                        'offset' => $combinationCounter[$combinationKey] // Offset starts at 1
                                                    ];
                                                    */
                                                    $productCount --;
                                                }
                                                else
                                                    $startingIndex --;
                                                $combinationCounter[$combinationKey]++;
                                            }
                                            if ($productCount == 0) {
                                                dump("combinationCounter", $combinationCounter);
                                                dump($results);
                                                return $results;
                                            }
                                        if ($counter == $totalRecords){
                                            dump("combinationCounter", $combinationCounter);
                                            dump($results);
                                            return $results;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function fyp_query(String $type, String $brand, String $color, int $offset) {
        $query = $this->entityManager->createQuery(
            'SELECT p 
                                                FROM App\Entity\Product p
                                                WHERE p.type = :type 
                                                AND p.brand = :brand 
                                                AND p.color = :color
                                                ORDER BY p.id ASC'  // Ensure deterministic order
        );
        $query->setParameter('type', $type);
        $query->setParameter('brand', $brand);
        $query->setParameter('color', $color);
        $query->setFirstResult($offset); // Skip rows (offset)
        $query->setMaxResults(1); // Get only one result

        $queryResult = $query->getOneOrNullResult();
        $this->entityManager->clear();

        return $queryResult;
    }

}


























?>