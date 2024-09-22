<?php

namespace App\Controller;


use App\Repository\ProductRepository;



use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Filesystem;

class ApiController extends AbstractController {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, Filesystem $filesystem)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/getProductByPopular/{qta}-{position}', name: 'get_product_by_popular', methods: ['GET'])]
    public function getProductByPopular(int $qta, int $position, ProductRepository $productRepository): JsonResponse
    {
        [$hasMore, $products] = $productRepository->findPopularProducts($qta, $position);

        $productData = array_map(function ($product) {
            return [
                'id' => $product->getId(),
                'model' => $product->getModel(),
                'image' => $product->getMainImage(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
                'seller'=>[
                    'username'=>$product->getSellerUsername()->getUsername(),
                ]
            ];
        }, $products);

        return new JsonResponse([
            'hasMore' => $hasMore,
            'products' =>    $productData,
        ]);
    }

    #[Route('/api/getProductByResearch/{qta}-{position}', name:'get_product_by_research')]
    public function getProductByResearch(Request $request, int $qta, int $position, ProductRepository $productRepository) :JsonResponse{

        $data = json_decode($request->getContent(), true);

        if ($data) {
            $research = $data['research'] ?? '';  // Search query
            $gender = $data['gender'] ?? [];      // Selected genders
            $age = $data['age'] ?? [];            // Selected ages
            $types = $data['types'] ?? [];        // Selected types
            $brands = $data['brands'] ?? [];      // Selected brands
            $colors = $data['colors'] ?? [];      // Selected colors
            $sizes = $data['sizes'] ?? [];      // Selected colors
        }
        [$hasMore, $products] = $productRepository -> findResearchedProduct($research, $gender, $age, $types, $brands, $colors, $sizes, $qta, $position);

        $productData = array_map(function ($product) {
            return [
                'id' => $product->getId(),
                'model' => $product->getModel(),
                'image' => $product->getMainImage(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
                'seller'=>[
                    'username'=>$product->getSellerUsername()->getUsername(),
                ]
            ];
        }, $products);

        //dump($data);
        //dump($products);

        return new JsonResponse([
            'hasMore' => $hasMore,
            'products' => $productData,
        ], 200);
    }

    #[Route('/api/fyp-function/{qta}-{position}',)]
    public function fyp_function($qta, $position, Request $request): Response
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
        [$hasMore, $items] = $this -> calculateItems($typeCookie, $brandCookie, $colorCookie, $position, $qta);
        $productData = array_map(function ($product) {
            return [
                'id' => $product->getId(),
                'model' => $product->getModel(),
                'image' => $product->getMainImage(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
                'seller'=>[
                    'username'=>$product->getSellerUsername()->getUsername(),
                ]
            ];
        }, $items);

        //dump($hasMore);
        return new JsonResponse([
            'hasMore' => $hasMore,
            'products' => $productData,
        ]);
    }

    private function getDivisionRate($first, $second): int {
        if ($second == 0){
            if ($first == 0)
                return 1;
            else
                return intval($first / 1);
        }
        if ($first / $second > 10) return -1;
        return intval($first / $second);
    }
    
    public function adjustPriorities($array) {
        for ($i=0; $i<count($array)-1; $i++) {
            for ($j = $i+1; $j<count($array); $j++) {
                $array[$i] *= $array[$j];
            }
        }
        return $array;
    }


    // Function to calculate and simulate the for loops described
    private function calculateItems($types, $brands, $colors, $startingIndex, $productCount): array {

        $typeSelected = $this->calculatePriority(array_keys($types), array_values($types));
        $typesPicked = $typeSelected[0];
        $typePriorities = $typeSelected[1];

        $brandSelected = $this->calculatePriority(array_keys($brands), array_values($brands));
        $brandsPicked = $brandSelected[0];
        $brandPriorities = $brandSelected[1];

        $colorSelected = $this->calculatePriority(array_keys($colors), array_values($colors));
        $colorsPicked = $colorSelected[0];
        $colorPriorities = $colorSelected[1];

        $typePriorities = $this->adjustPriorities($typePriorities);
        $brandPriorities = $this->adjustPriorities($brandPriorities);
        $colorPriorities = $this->adjustPriorities($colorPriorities);

        //dump("typeDivisions: ", $types, $typePriorities);
        //dump("brandDivisions: ", $brands, $brandPriorities);
        //dump("colorDivisions: ", $colors, $colorPriorities);
        //dump($typesPicked, $brandsPicked, $colorsPicked);

        $totalRecords = $this -> getTotalPossibleFypProducts($typesPicked, $brandsPicked, $colorsPicked) - $startingIndex;

        //dump('totalRemainingRecords', $totalRecords);

        //$this->entityManager->getConnection()->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        // Step 4: Use loops based on the divisions found
        $results = [];
        $combinationCounter = []; // To store occurrences of each combination
        $counter = 0;
    
        //for ($i = 0; $i < $productCount; $i++) {
        while (true) {
            
            foreach ($typePriorities as $typeIndex => $typeLoopCount) {
                for ($j = 0; $j < $typeLoopCount; $j++) {

                    foreach ($brandPriorities as $brandIndex => $brandLoopCount) {
                        for ($k = 0; $k < $brandLoopCount; $k++) {

                            foreach ($colorPriorities as $colorIndex => $colorLoopCount) {
                                for ($l = 0; $l < $colorLoopCount; $l++) {

                                    $type = $typesPicked[$typeIndex];
                                    $brand = $brandsPicked[$brandIndex];
                                    $color = $colorsPicked[$colorIndex];
                                    // Create a combination key to track how many times the product with the same type, brand and color has occurred
                                    $combinationKey = $type . '-' . $brand . '-' . $color;

                                    // Initialize counter for this combination if not set
                                    if (!isset($combinationCounter[$combinationKey])) {
                                        $combinationCounter[$combinationKey] = 0; //null offset is 1
                                    }
                                    //dump("combinationCounter", $combinationCounter);
                                    if ( $combinationCounter[$combinationKey] == -1 ){
                                        continue;
                                    }
             

                                        $queryResult = $this->fyp_query($type, $brand, $color, $combinationCounter[$combinationKey]);

                                        if ($queryResult === null) {
                                            //dump('Query not ok' . $type . $brand . $color . $combinationCounter[$combinationKey]);
                                            $combinationCounter[$combinationKey] = -1;
                                        } else {

                                            //dump("productCount" . $productCount);

                                            if ($startingIndex == 0 && $productCount >= 1) {

                                                //dump("query-ok". $type . $brand . $color . $combinationCounter[$combinationKey]);
                                                $results[] = $queryResult;
                                                $counter++;
                                                $productCount--;
                                            } else
                                                $startingIndex--;
                                        }
                                        $combinationCounter[$combinationKey]++;
                                        if ($productCount == 0) {
                                            //dump("combinationCounter", $combinationCounter);
                                            //dump($results);
                                            //dump($totalRecords, $productCount, $totalRecords>$productCount);
                                            return [$totalRecords>$counter, $results];
                                        }
                                        if ($counter == $totalRecords) {
                                            //dump("combinationCounter", $combinationCounter);
                                            //dump($results);
                                            //dump($totalRecords, $productCount, $totalRecords>$productCount);
                                            return [$totalRecords>$counter, $results];
                                        }
                                    
                                } //color loop
                            } //foreach color
                        } //brand loop
                    } //foreach brand
                } //type loop
            } //foreach type
        } //while true
    }
    
    private function calculatePriority(array $names, array $visits): array {
        $priorities = [];
        $totalItems = 0;
        $totalRate = 0;
        $pickedNames = [];
        for ($i = 1; $i < count($visits); $i++) {
            $divResult = $this -> getDivisionRate($visits[$i - 1], $visits[$i]);
            if ($divResult == -1 || $totalRate >= 10 || count($pickedNames) >= 2) {
                $priorities[] = 1;
                $pickedNames[] = $names[$i - 1];
                break;
            }
            $priorities[] = $divResult;
            $pickedNames[] = $names[$i - 1];
            $totalRate += $divResult;
            $totalItems ++;
        }
        return [$pickedNames, $priorities];
    }


    private function getTotalPossibleFypProducts($pickedTypeNames, $pickedBrandNames, $pickedColorNames) {

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

        return $query->getSingleScalarResult();
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