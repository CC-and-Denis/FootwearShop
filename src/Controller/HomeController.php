<?php

namespace App\Controller;

use App\Repository\RatingRepository;
use App\Service\CookieService;
use App\Form\RatingType;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Rating;
use App\Form\ProductFormType;
use App\Form\PaymentType;

use Stripe\Stripe;
use Stripe\PaymentIntent;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
    }

    #[Route('/home', name:'home')]
    public function loadHomePage(CookieService $cookieService, Request $request): Response
    {
        if (json_decode($request->cookies->get('type'), true) == null) {
            $cookies = $cookieService -> cookie_creation();

            $typeJSON = json_encode($cookies['type']);
            $brandJSON = json_encode($cookies['brand']);
            $colorJSON = json_encode($cookies['color']);

            $response = new Response();

            $response->headers->setCookie(new Cookie('type', $typeJSON, strtotime('2200-01-01 00:00:00')));
            $response->headers->setCookie(new Cookie('brand', $brandJSON, strtotime('2200-01-01 00:00:00')));
            $response->headers->setCookie(new Cookie('color', $colorJSON, strtotime('2200-01-01 00:00:00')));
            dump($cookies);

            $content = $this->renderView('homepage.html.twig', ['cookies' => $cookies]);
            $response->setContent($content);
            return $response;
        }
        return $this->render('homepage.html.twig',);

    }

    #[Route('/chatpage', name:'chatpage')]
    public function loadChatPage() {

        return $this->render('chat_page.html.twig', );
    }

    #[Route('product/{productId}', )]
    public function loadProductPage(CookieService $cookieService, $productId, Request $request): Response
    {
        $response = new Response();
        $productRepository = $this->entityManager->getRepository(Product::class);
        $userRepository= $this->entityManager->getRepository(User::class);
        $targetProduct = $productRepository->findOneBy(['id' => $productId]);
        $renderVariables = [];

        if(! $targetProduct){
            return $this->render('not_found.html.twig', [
                'entity' => "Product"
            ], $response);
        }
        $renderVariables['product'] = $targetProduct;


        if($this->getUser()==$targetProduct->getVendor() || $targetProduct->getQuantity()<=0){
            return $this->render('product/product_view_page.html.twig',$renderVariables, $response);
        }

        $targetProduct->increaseViews();
        $cookieService->cookie_update($targetProduct, $request, $response);

        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        $errorsList = $form->getErrors(true);

        $renderVariables['form'] = $form->createView();
        $renderVariables['errorsList'] = $errorsList;

        if ($form->isSubmitted() && $form->isValid()) {

                $buyer = $userRepository->findOneBy(['username' => $this->getUser()->getUsername()]);

                $customer = \Stripe\Customer::create([
                    'name' => $buyer->getName() . ' ' . $buyer->getSurname(),
                    'email' => $buyer->getEmail(),
                ]);

                //ups or other deliveries api

                header('Content-Type: application/json');

                $YOUR_DOMAIN = 'http://localhost:8000';

                $session = \Stripe\Checkout\Session::create([
                  'payment_method_types'=>['card'],
                  'customer' => $customer->id,
                  //'name'=> ( $buyer->getName() . $buyer->getSurname() ),
                  'line_items' => [[
                    # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                    'price_data'=>[
                        'currency'=>'eur',
                        'product_data'=>[
                            'name'=>$targetProduct->getModel(),
                            'description'=>$targetProduct->getDescription(),
                        ],
                    'unit_amount' => ($targetProduct->getPrice()*100),

                    ],
                    'quantity' => 1,
                  ]],
                  'metadata'=>[
                    'user_id' => $buyer->getId(),
                    'product_id' => $targetProduct->getId(),
                  ],
                
                  'mode' => 'payment',
                  'success_url' => $this->generateUrl('home',['productId'=>$targetProduct->getId(), 'sessionId' => '{CHECKOUT_SESSION_ID}'],UrlGeneratorInterface::ABSOLUTE_URL),
                  'cancel_url' => $this->generateUrl('productPage',['productId'=>$targetProduct->getId()],UrlGeneratorInterface::ABSOLUTE_URL),
                ]);

                header("HTTP/1.1 303 See Other");
                header("Location: " . $session->url);

                

               $targetProduct->decreaseQuantity();

            
                $this->entityManager->persist($targetProduct);
                $this->entityManager->flush();

                return $this->redirect($session->url,303);
            }
        

        $this->entityManager->persist($targetProduct);
        $this->entityManager->flush();

        return $this->render('product/product_view_page.html.twig',$renderVariables, $response);
            
    }

    #[Route('/stripe_webhooks', name: 'stripe_webhook', methods: ['POST'])]
    public function webhookStripeListener() {
        $endpoint_secret = $_ENV['STRIPE_WEBHOOK_SECRET'];
    
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;
        $newTransaction = new Order();
        
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            http_response_code(525);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            http_response_code(525);
            exit();
        }
    
        if (
            $event->type == 'checkout.session.completed' ||
            $event->type == 'checkout.session.async_payment_succeeded'
        ) {

            $session = $event->data->object;

            // Retrieve metadata
            $userId = $session->metadata->user_id;
            $productId = $session->metadata->product_id;

            $targetProduct=$this->entityManager->getRepository(Product::class)->findOneBy(['id' => $productId]);


            $newTransaction->setPurchaseDate(new \DateTime());
            $newTransaction->setUser($this->entityManager->getRepository(User::class)->findOneBy(['id' => $userId])); 
            $newTransaction->setProduct($targetProduct);

    
            $checkout_session = \Stripe\Checkout\Session::retrieve($event->data->object->id, [
                'expand' => ['line_items'],
            ]);
    
            if ($checkout_session->payment_status != 'unpaid') {
                $targetProduct->increaseItemsSold();
                $newTransaction->setPaymentStatus("success");
            } else {
                $newTransaction->setPaymentStatus("error");
                $targetProduct->increaseQuantity();
            }
    
            $this->entityManager->persist($targetProduct);
            $this->entityManager->persist($newTransaction);
            $this->entityManager->flush();
    
            http_response_code(200);
            exit();
        }
    
        http_response_code(200);
        exit();
    }
    

 


    #[Route('createproduct'),]
    public function createProduct(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        
        $form->handleRequest($request);

        $errorsList = $form->getErrors(true);

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
                $newProduct->setVendor($this->getUser());
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
            'form'=>$form->createView(),
            'errorsList'=>$errorsList,
        ]);
    }


    #[Route('deleteproduct/{id}',name:"delete_product")]
    public function deleteProduct(int $id){

        $productRepository = $this->entityManager->getRepository(Product::class);
        $targetProduct = $productRepository->findOneBy(['id' => $id]);
        if( (! $targetProduct) || $targetProduct->getVendor()->getUsername()!=$this->getUser()->getUsername() || $targetProduct->getItemsSold()!=0 ){
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

/*
    #[Route('/research', name:'research')]
    public function research(Request $request) :Response{
        //form creation

        if ($form->isSubmitted() && $form->isValid()) {
            $researchData = $form->getData();
            $data = [
                'type' => $researchData->type,
                'brand' => $researchData->brand,
                'color' => $researchData->color,
                'gender' => $researchData->gender,
                'researchbar' => $researchData->researchbar,
            ];
            return $data;++
        }
    }

*/


    #[Route('editproduct/{id}', name:'edit_product')]
    public function editProduct(int $id,Request $request){

        $productRepository = $this->entityManager->getRepository(\App\Entity\Product::class);
        $product = $productRepository->findOneBy(['id' => $id]);

        if( (! $product) || $product->getVendor()->getUsername()!=$this->getUser()->getUsername() ){
            return $this->render('not_found.html.twig',[
                'entity' => 'Product'
            ]);
        }

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);
        $errorsList = $form->getErrors(true);


        if($form->isSubmitted() && $form->isValid()){
            
            $product=$form->getData();
            $quantity = $form->get('quantity')->getData();

            if ($form->has('otherImages') && $form->get('otherImages')->getData()) {

                $otherImages = $form->get('otherImages')->getData();
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
            
            if($quantity<0){
                $product->setQuantity(0);
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
            'errorsList'=>$errorsList,
        ]);

    }


    #[Route('user/{username}',)]
    public function loadUserPage(String $username, Request $request): Response
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $targetUser = $userRepository->findOneBy(['username' => $username]);
        $renderVariables = [];
        $productsBoughtFromTarget=[];

        if(! $targetUser){
            return $this->render('not_found.html.twig',[
                'entity'=>"User"
            ]);
        }

        $renderVariables["targetUser"] = $targetUser;

        $currentUser = $userRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);

        $renderVariables["owner"] = false;
        if($targetUser->getUsername() == $this->getUser()->getUserIdentifier()){
            $renderVariables["owner"] = true;
            $renderVariables["orders"] = $targetUser->getOrders();

        }
        
        $productsBoughtFromTarget = $currentUser->hasBoughtFrom($targetUser);

        if ( count($productsBoughtFromTarget) ) {
            $renderVariables["orders"] = $productsBoughtFromTarget;
            $renderVariables["reviews"] = $currentUser->didReview($targetUser);
        }

        return $this->render('user/user_page.html.twig',$renderVariables);

    }



    #[Route('/transactions_history', name: 'transactions_history')]

    public function loadTransactionsHistoryPage() {
        return $this->render('transactions_history.html.twig');
    }



    #[Route('/fyp',name:"load_popular_products_page")]
    public function loadFyProductsPage(){
        return $this->render('product_grid_page.html.twig',['item' => ['page_name' => 'fyp']]);}



    #[Route('/populars',name:"load_popular_products_page")]
    public function loadPopularProductsPage(){
        return $this->render('product_grid_page.html.twig',['item' => ['page_name' => 'populars']]);}

}
?>