<?php

namespace App\Controller;


use App\Repository\ProductRepository;

use App\Service\FyService;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Filesystem;

class ApiController extends AbstractController {

    private EntityManagerInterface $entityManager;
    private FyService $fyService;

    public function __construct(EntityManagerInterface $entityManager, FyService $fyService)
    {
        $this->entityManager = $entityManager;
        $this->fyService = $fyService;
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
                'quantity' => $product->getQuantity(),
                'seller'=>[
                    'username'=>$product->getVendor()->getUsername(),
                ]
            ];
        }, $products);

        return new JsonResponse([
            'hasMore' => $hasMore,
            'products' => $productData,
        ]);
    }

    #[Route('/api/getProductByResearch/{qta}-{position}', name:'get_product_by_research')]
        public function getProductByResearch(Request $request, int $qta, int $position, ProductRepository $productRepository) :JsonResponse{

        $data = json_decode($request->getContent(), true);

        if ($data) {
            $research = $data['research'] ?? '';
            $gender = $data['gender'] ?? [];
            $age = $data['age'] ?? [];
            $types = $data['types'] ?? [];
            $brands = $data['brands'] ?? [];
            $colors = $data['colors'] ?? [];
            $sizes = $data['sizes'] ?? [];

            [$hasMore, $products] = $productRepository->findResearchedProduct($research, $gender, $age, $types, $brands, $colors, $sizes, $qta, $position);
            dump($products);
            $productData = array_map(function ($product) {
                return [
                    'id' => $product->getId(),
                    'model' => $product->getModel(),
                    'image' => $product->getMainImage(),
                    'price' => $product->getPrice(),
                    'description' => $product->getDescription(),
                    'quantity' => $product->getQuantity(),
                    'seller' => [
                        'username' => $product->getVendor()->getUsername(),
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
        return new JsonResponse(['hasMore' => false, 'products' => null]);
    }

    #[Route('/api/getProductsForYou/{qta}-{position}',)]
    public function getProductsForYou($qta, $position, Request $request): Response
    { 
        $typeCookie = json_decode($request->cookies->get('type'), true);
        $brandCookie = json_decode($request->cookies->get('brand'), true);
        $colorCookie = json_decode($request->cookies->get('color'), true);

        arsort($typeCookie);  // Sorting the type array
        arsort($brandCookie); // Sorting the brand array
        arsort($colorCookie); // Sorting the color array
    
        if (empty($typeCookie) || empty($brandCookie) || empty($colorCookie)) {
            return $this->redirectToRoute('/home');
        }
    
        // Calculate items based on the provided conditions
        [$hasMore, $items] = $this->fyService->calculateItems($typeCookie, $brandCookie, $colorCookie, $position, $qta);
        $productData = array_map(function ($product) {
            return [
                'id' => $product->getId(),
                'model' => $product->getModel(),
                'image' => $product->getMainImage(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
                'quantity' => $product->getQuantity(),
                'seller'=>[
                    'username'=>$product->getVendor()->getUsername(),
                ]
            ];
        }, $items);

        //dump($hasMore);
        return new JsonResponse([
            'hasMore' => $hasMore,
            'products' => $productData,
        ]);
    }

    #[Route('/api/getSimilarProducts/{qta}-{position}-{productId}', name: 'get_similar_products')]
    public function getSimilarProducts(Request $request, $qta, $position, $productId, ProductRepository $productRepository): JsonResponse {
            [$hasMore, $items] = $productRepository -> findSimilarProduct($productId, $qta, $position);
            $productData = array_map(function ($product) {
                return [
                    'id' => $product->getId(),
                    'model' => $product->getModel(),
                    'image' => $product->getMainImage(),
                    'price' => $product->getPrice(),
                    'description' => $product->getDescription(),
                    'quantity' => $product->getQuantity(),
                    'seller' => [
                        'username' => $product->getVendor()->getUsername(),
                    ]
                ];
            }, $items);

            dump($productData);

            return new JsonResponse([
                'hasMore' => $hasMore,
                'products' => $productData,
            ], 200);
    }
}

?>