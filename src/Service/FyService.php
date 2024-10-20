<?php

namespace App\Service;

use App\Repository\ProductRepository;

class FyService
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    public function calculateItems($types, $brands, $colors, $startingIndex, $productCount): array {

        [$typesPicked, $typePriorities] = $this->calculatePriority(array_keys($types), array_values($types));

        [$brandsPicked, $brandPriorities] = $this->calculatePriority(array_keys($brands), array_values($brands));

        [$colorsPicked, $colorPriorities] = $this->calculatePriority(array_keys($colors), array_values($colors));

        $typePriorities = $this->adjustPriorities($typePriorities);
        $brandPriorities = $this->adjustPriorities($brandPriorities);
        $colorPriorities = $this->adjustPriorities($colorPriorities);   

        $totalRecords = $this->productRepository->getTotalPossibleFyProducts($typesPicked, $brandsPicked, $colorsPicked) - $startingIndex;

        $results = [];
        $combinationCounter = []; // To store occurrences of each combination
        $counter = 0;

        dump($types, $brands, $colors);
        dump($typesPicked, $brandsPicked, $colorsPicked);
    
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

                                    // Initialize offset counter for this combination if not set
                                    if (!isset($combinationCounter[$combinationKey])) {
                                        $combinationCounter[$combinationKey] = 0;
                                    }
                                    if ( $combinationCounter[$combinationKey] == -1 ){
                                        continue;
                                    }
                                        $queryResult = $this->productRepository->fy_query($type, $brand, $color, $combinationCounter[$combinationKey]);
                                        dump($type, $brand, $color);
                                        if ($queryResult === null) {
                                            dump('Query not ok' . $type . $brand . $color . $combinationCounter[$combinationKey]);
                                            $combinationCounter[$combinationKey] = -1;
                                        } else {
                                            dump("productCount" . $productCount);

                                            if ($startingIndex == 0 && $counter<$productCount) {

                                                dump("query-ok". $type . $brand . $color . $combinationCounter[$combinationKey]);
                                                $results[] = $queryResult;
                                                $counter++;
                                            } else
                                                $startingIndex--;
                                            $combinationCounter[$combinationKey]++;
                                        }
                                        if ($counter == $productCount) {
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
            } //foreach types
        } //while true
    }
    
    private function calculatePriority(array $specifics, array $visits): array {
        $priorities = [];
        $totalItems = 0;
        $totalRate = 0;
        $pickedNames = [];
        for ($i = 1; $i < count($visits); $i++) {
            $divResult = $this -> getDivisionRate($visits[$i - 1], $visits[$i]);
            if ($divResult == -1 || $totalRate >= 10 || count($pickedNames) >= 2) { //max 3 specifics picked
                $priorities[] = 1;
                $pickedNames[] = $specifics[$i - 1];
                break;
            }
            $priorities[] = $divResult;
            $pickedNames[] = $specifics[$i - 1];
            $totalRate += $divResult;
            $totalItems ++;
        }
        return [$pickedNames, $priorities];
    }

    public function adjustPriorities($array) {
        for ($i=0; $i<count($array)-1; $i++) {
            for ($j = $i+1; $j<count($array); $j++) {
                $array[$i] *= $array[$j];
            }
        }
        return $array;
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
}





?>