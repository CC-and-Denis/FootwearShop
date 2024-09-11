<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Form\PaymentType;
use App\Repository\ProductRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Filesystem;  
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, Filesystem $filesystem)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/home', name:'home')]
    public function loadHomePage(): Response
    {
        return $this->render('homepage.html.twig',);

    }

    #[Route('product/{productId}', )]
    public function loadProductPage($productId, Request $request): Response
    {

        $response = new Response();
        $productRepository = $this->entityManager->getRepository(Product::class);
        $targetProduct = $productRepository->findOneBy(['id' => $productId]);
        $remainingProducts = ( $targetProduct->getQuantity() - $targetProduct->getItemsSold() );
        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        

        if( $form->isSubmitted() && $form->isValid() ){
            if( $remainingProducts ){
                $response = new Response('There are no items left', 500);
                return $response;
            }

            $mainImagePath=$form->get('cardNumber')->getData();
            $otherImages=$form->get('expMonth')->getData();
            $otherImages=$form->get('expYear')->getData();
            $quantity = $form->get('cvc')->getData();

            //use stripe here  
            return $this->redirectToRoute('homepage');
        }

        if($targetProduct){
            $this -> cookie_update($targetProduct, $request, $response);
            return $this->render('product/product_view_page.html.twig',[
                'product'=>$targetProduct,
            ], $response
        );
        }
        else{
            return $this->render('not_found.html.twig',[
                'entity'=>"Product"
            ], $response);
        }
        
    }



    private function cookie_update(Product $product, Request $request, $response) {
    
        $typeCookie = $request->cookies->get('type');
        $brandCookie = $request->cookies->get('brand');
        $colorCookie = $request->cookies->get('color');


        $types = json_decode($typeCookie, true);
        $brands = json_decode($brandCookie, true);
        $colors = json_decode($colorCookie, true);

        $types[$product->getType()] ++;
        $brands[$product->getBrand()] ++;
        $colors[$product->getColor()] ++;

        //dump($types, $brands, $colors);
        
        $typeJSON = json_encode($types);
        $brandJSON = json_encode($brands);
        $colorJSON = json_encode($colors);

        $response->headers->setCookie(new Cookie('type', $typeJSON, strtotime('2200-01-01 00:00:00')));
        $response->headers->setCookie(new Cookie('brand', $brandJSON, strtotime('2200-01-01 00:00:00')));
        $response->headers->setCookie(new Cookie('color', $colorJSON, strtotime('2200-01-01 00:00:00')));
        
    
        return $response;
    }

    #[Route('createproduct'),]
    public function createProduct(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        
        $form->handleRequest($request);
        //dd($form->getErrors());
        if($form->isSubmitted() && $form->isValid()){
            
            $newProduct = $form->getData();
            $mainImagePath = $form->get('mainImage')->getData();
            $otherImages = $form->get('otherImages')->getData();
            $quantity = $form->get('quantity')->getData();
            


            if($mainImagePath){

                $newMainIMageName=uniqid() . '.' . $mainImagePath->guessExtension();

                try{
                    $mainImagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newMainIMageName
                    );

                }catch(FileException $e){
                    return new Response($e->getMessage());
                }
            
                if ($otherImages) {
                    foreach ($otherImages as $image) {
                            $newFilename = uniqid() . '.' . $image->guessExtension();
                            try {
                                $image->move(
                                    $this->getParameter('kernel.project_dir') . '/public/uploads',
                                    $newFilename
                                );
        
                                // Add the path to the entity as a string
                                $product->addOtherImage('/uploads/' . $newFilename);
                            } catch (FileException $e) {
                                // Handle the exception gracefully
                                return new Response($e->getMessage());
                            }
                
                    }
                }

                $newProduct->setMainImage('/uploads/'.$newMainIMageName);
                $newProduct->setViews(0);
                $newProduct->setItemsSold(0);
                $newProduct->setSellerUsername($this->getUser());
                if($quantity<1){
                    $newProduct->setQuantity(1);
                }else{
                    $newProduct->setQuantity($quantity);
                }
                

            }

            $this->entityManager->persist($newProduct);
            $this->entityManager->flush();

            return $this->redirectToRoute("userPage",[
               "username"=> $this->getUser()->getUsername()
            ]);
        } 
        
        return $this->render('product/product_form_page.html.twig',[
            'form'=>$form->createView()
        ]);
    }


    #[Route('deleteproduct/{id}',name:"delete_product")]
    public function deleteProduct(int $id){
        $productRepository = $this->entityManager->getRepository(Product::class);
        $targetProduct = $productRepository->findOneBy(['id' => $id]);
        if( (! $targetProduct) || $targetProduct->getSellerUsername()->getUsername()!=$this->getUser()->getUsername() || $targetProduct->getItemsSold()!=0 ){
            $response = new Response("This item can't be deleted",500);
            return $response;
        }
        //product deletable
        $this->entityManager->remove($targetProduct);
        unlink($this->getParameter('kernel.project_dir') .'/public'.$targetProduct->getMainImage());
        foreach ($targetProduct->getOtherImages() as $image) {
            unlink($this->getParameter('kernel.project_dir') .'/public'.$image);
        }
        $this->entityManager->flush();


        $response = new Response("item deleted",200);
        return $response;
    }

    #[Route('editproduct/{id}',name:'edit_product')]
    public function editProduct(int $id,Request $request){
        $productRepository = $this->entityManager->getRepository(\App\Entity\Product::class);
        $product = $productRepository->findOneBy(['id' => $id]);

        if( (! $product) || $product->getSellerUsername()->getUsername()!=$this->getUser()->getUsername() ){
            return $this->render('not_found.html.twig',[
                'entity' => 'Product'
            ]);
        }

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            
            $product=$form->getData();
            $quantity = $form->get('quantity')->getData();
            if ($form->has('otherImages')) {
                $otherImages = $form->get('otherImages')->getData();
                if ($otherImages) {
                    foreach ($otherImages as $image) {
                    
                            $newFilename = uniqid() . '.' . $image->guessExtension();
        
                            try {
                                $image->move(
                                    $this->getParameter('kernel.project_dir') . '/public/uploads',
                                    $newFilename
                                );
        
                                // Add the path to the entity as a string
                                $product->addOtherImage('/uploads/' . $newFilename);
                            } catch (FileException $e) {
                                // Handle the exception gracefully
                                return new Response($e->getMessage());
                            }
                
                    }
                }
            }
            
            if($quantity<1){
                $product->setQuantity(1);
            }else{
                $product->setQuantity($quantity);
            }

            

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute("userPage",[
               "username"=> $this->getUser()->getUsername()
            ]);
        } 
        return $this->render('product/product_form_page.html.twig',[
            'form'=>$form->createView(),
            'product'=>$product,
        ]);

        


    }


    #[Route('user/{username}',)]
    public function loadUserPage(String $username): Response
    {
        $userRepository = $this->entityManager->getRepository(\App\Entity\User::class);
        $targetUser = $userRepository->findOneBy(['username' => $username]);

        if($targetUser){
            return $this->render('user/user_page.html.twig',[
                'username'=>$username,
                'isVendor'=>$targetUser->isVendor(),
                'products'=>$targetUser->getSellingProducts(),
            ]);
        }else{
            return $this->render('not_found.html.twig',[
                'entity'=>"User"
            ]);
        }
    }


    #[Route('/api/getProductByPopular/{qta}-{position}', name: 'get_product_by_popular', methods: ['GET'])]
    public function getProductByPopular(int $qta, int $position, ProductRepository $productRepository): Response
    {
        // Fetch the popular products from the repository
        $products = $productRepository->findPopularProducts($qta, $position);
        $hasMore = count($products) === $qta;

        return $this->render('product/product_card_component.html.twig',[
            "products"=>$products,
            'hasMore' => $hasMore,
        ]);
    }
#[Route('/populars',name:"load_popular_products_page")]
public function loadPopularProductsPage(){
    return $this->render('populars_page.html.twig',[]);}

    #[Route('/api/getProductForFyp/{qta}-{position}', name: 'get_product_for_fyp', methods: ['GET'])]
    public function getProductForFyp(int $qta, int $position, ProductRepository $productRepository, Request $request): Response
    {
        // Fetch the popular products from the repository
        $typeCookie = json_decode($request->cookies->get('type'), true);
        $brandCookie = json_decode($request->cookies->get('brand'), true);
        $colorCookie = json_decode($request->cookies->get('color'), true);

        arsort($typeCookie);  // Sorting the type array
        arsort($brandCookie); // Sorting the brand array
        arsort($colorCookie); // Sorting the color array
    
        if (empty($typeCookie) || empty($brandCookie) || empty($colorCookie)) {
            return $this->render('product/homepage.html.twig',);
        }
    
        // Calculate items based on the provided conditions
        $products = $this -> calculateItems($typeCookie, $brandCookie, $colorCookie, $position, $qta, $productRepository);
        $hasMore = count($products) === $qta;

        return $this->render('product/product_card_component.html.twig',[
            "products"=>$products,
            'hasMore' => $hasMore,
        ]);
    }

    #[Route('/api/fyp-function/{qta}-{position}',)]
    
        // Main function to handle cookie data and the item calculation
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

        //dump($items);
    
        // Render the items (for now, just print them)
        /*
        foreach ($items as $index => $item) {
            dd( ($index + 1) . ": Type: " . $item['type'] . ", Brand: " . $item['brand'] . ", Color: " . $item['color'] . " | Offset: " . $item['offset'] . "<br>");
        }
        */
        $hasMore = count($items ) === $qta;

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

        //dump("typeDivisions: ", $types, $typeDivisions);
        //dump("brandDivisions: ", $brands, $brandDivisions);
        //dump("colorDivisions: ", $colors, $colorDivisions);
        //dump($pickedTypeNames, $pickedBrandNames, $pickedColorNames);
        
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

        //dump('totalRecords', $totalRecords);

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
                                    //dump($type, $brand, $color, $combinationCounter[$combinationKey]);
                                    //dump("combinationCounter", $combinationCounter);
                                    if ($combinationCounter[$combinationKey] != -1) {
                                        if ($startingIndex == 0){

                                            //$queryResult = $productRepository -> findFavouriteProduct($type, $brand, $color, $combinationCounter[$combinationKey]);
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
                                            $query->setFirstResult($combinationCounter[$combinationKey]); // Skip rows (offset)
                                            $query->setMaxResults(1); // Get only one result
                                            
                                            $queryResult = $query->getOneOrNullResult();
                                            $this->entityManager->clear();

                                            if ($queryResult === null) {
                                                $combinationCounter[$combinationKey] = -1;
                                            }
                                            else{

                                                //dump("productCount", $productCount);
                                                
                                                if ($productCount >= 1) {

                                                    //dump("Pinkyeah", $type, $brand, $color, $combinationCounter[$combinationKey]);
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
                                                $combinationCounter[$combinationKey]++;
                                            }
                                            if ($productCount == 0) {
                                                //dump("combinationCounter", $combinationCounter);
                                                return $results;
                                            }
                                        }
                                        else
                                            $startingIndex --;
                                        if ($counter == $totalRecords){
                                            //dump("combinationCounter", $combinationCounter);
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







    

    private function createQuery($type, $brand, $color, $offset) {
        $queryBuilder = $this -> entityManager->getRepository(Product::class)->createQueryBuilder('e');

        // Filter on multiple fields
        /*
        $queryBuilder
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('e.type', ':field1'),
                $queryBuilder->expr()->eq('e.brand', ':field2'),
                $queryBuilder->expr()->eq('e.color', ':field3')
            ))
            ->setParameter('field1', $type)
            ->setParameter('field2', $brand)
            ->setParameter('field3', $color)
            ->setFirstResult($offset) // offset (0-based)
            ->setMaxResults(1); // number of result rows
            return $queryBuilder->getQuery()->getOneOrNullResult();
        */
        return $this -> entityManager->getRepository(Product::class)->createQueryBuilder('p')
            ->where('p.type = :type')
            ->andWhere('p.brand = :brand')
            ->andWhere('p.color = :color')
            ->setParameter('type', $type)
            ->setParameter('brand', $brand)
            ->setParameter('color', $color)
            ->setMaxResults(1)
            ->setFirstResult($offset) // Using the offset parameter
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Step 2: Normalize the probabilities (fractions)
    private function normalizeProbabilities($names) {
        $totalProbability = array_sum($names); // Get the total sum of all probabilities
        $probabilities = [];

        // For each name, calculate its relative probability
        foreach ($names as $name => $value) {
            $probabilities[$name] = $value / $totalProbability;
        }

        return $probabilities;
    }

    // Step 3: Pick a random name based on the probability distribution
    private function pickName($probabilities) {
        $rand = mt_rand() / mt_getrandmax(); // Generate a random float between 0 and 1
        $cumulativeProbability = 0;

        // Loop through each name and its probability
        foreach ($probabilities as $name => $probability) {
            $cumulativeProbability += $probability;

            // If the random value is less than the cumulative probability, return the name
            if ($rand < $cumulativeProbability) {
                return $name;
            }
        }

        return null; // Fallback in case of an issue
    }

    // Step 4: Pick 3 names using the method of extraction
    private function pickNames($probabilities, $n) {
        $selectedNames = [];

        for ($i = 0; $i < $n; $i++) {
            $selectedNames[$i] = $this -> pickName($probabilities); // Pick a name using the probabilities
        }

        return $selectedNames;
    }


    private function read_json_file($name) {
        $path = $this->getParameter($name);
        if (!file_exists($path)) {
            $this->filesystem->dumpFile($path, json_encode([])); // Create an empty JSON array
        }
        $data = file_get_contents($path);
        return json_decode($data, true); // true for associative array
    }




}
?>