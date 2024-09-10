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
        return $this->render('homepage.html.twig', [
            'item'=> [
                'model' => 'hey',
            ]
        ]);
    }

    #[Route('product/{productId}', )]
    public function loadProductPage($productId, Request $request): Response
    {

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
            return $this->render('product/product_view_page.html.twig',[
                'product' => $targetProduct,
                'form' => $form,
            ]
        );
        }else{
            return $this->render('not_found.html.twig',[
                'entity' => 'Product'
            ]);
        }
        
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

    #[Route('/api/fyp-function',)]
    public function fyp_function(Request $request): JsonResponse
    {


        // Get 'type' cookies values
        $typeCookie = json_decode($request->cookies->get('type'), true);
        $brandCookie = json_decode($request->cookies->get('brand'), true);
        $colorCookie = json_decode($request->cookies->get('color'), true);


        $normalizedProbabilities = $this -> normalizeProbabilities($typeCookie);
        // Pick 3 names based on probabilities
        $types = $this -> pickNames($normalizedProbabilities, 4);

        $normalizedProbabilities = $this -> normalizeProbabilities($brandCookie);
        $brands = $this -> pickNames($normalizedProbabilities, 4);

        $normalizedProbabilities = $this -> normalizeProbabilities($colorCookie);
        $colors = $this -> pickNames($normalizedProbabilities, 4);
        $data = [];

        $query_history = $this -> read_json_file('fyp_history_json');

        $queryResult = null ;

        for ($i = 0; $i < 1; $i++) {
            $queryResult = null ;
            while ($queryResult === null) {

                $v = 0;
                $n = 0;

                $this->filesystem->dumpFile($this->getParameter('prova_json'), json_encode($query_history));    

                if (gettype($query_history) === 'array' && $query_history != null) {
                    foreach ($query_history as $element) {
                        if ($element['query_r'] -> getType() ===  $types[$i] && $element['query_r'] -> getBrand() === $brands[$i] && $element['query_r'] -> getColor() === $colors[$i]) {
                            $n = $element['n'];
                            $element['n'] = $n + 1;
                            $v = 1;
                            break;
                        }
                    }
                }
                else {
                    $query_history = [];
                }
                if ($v === 1) {
                    $queryResult = $this -> createQuery($types[$i], $brands[$i], $colors[$i], $n);
                }
                else {
                    $queryResult = $this -> createQuery($types[$i], $brands[$i], $colors[$i], 1);
                    /*
                    dd($queryResult);
                    if ($queryResult != null) {

                        array_push($query_history, [
                            'query_r' => $queryResult,
                            'n' => 1
                        ]);
                    }
                    */
                }

                $prova = [$types[$i], $brands[$i], $colors[$i]];

                $this->filesystem->dumpFile($this->getParameter('prova_json'), json_encode($prova));    
            }

            if ($queryResult === null) {
                // Handle the case when no result is found
                return new JsonResponse(['error' => 'No matching product found'], 404);
            }

            $data[$i] = [
                'id' => $queryResult -> getId(),
                'main_image' => $queryResult -> getMainImage(),
                'brand' => $queryResult -> getBrand(),
                'model' => $queryResult -> getModel(),
                'color' => $queryResult -> getColor(),
                'description' => $queryResult -> getDescription(),
            ];
            
            //dd($data);

            array_push($query_history, $queryResult);

        }

        $this->filesystem->dumpFile($this->getParameter('fyp_history_json'), json_encode($query_history));

        return new JsonResponse($data);
    }

    private function createQuery($value1, $value2, $value3, $start_row) {
        $queryBuilder = $this -> entityManager->getRepository(Product::class)->createQueryBuilder('e');

        // Filter on multiple fields
        $queryBuilder
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('e.type', ':field1'),
                $queryBuilder->expr()->eq('e.brand', ':field2'),
                $queryBuilder->expr()->eq('e.color', ':field3')
            ))
            ->setParameter('field1', $value1)
            ->setParameter('field2', $value2)
            ->setParameter('field3', $value3)
            ->setFirstResult($start_row) // offset (0-based)
            ->setMaxResults(1); // number of result rows
            return $queryBuilder->getQuery()->getOneOrNullResult();
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




}?>