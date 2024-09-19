<?php

namespace App\Controller;

use App\Form\ResearchType;
use App\Service\CookieService;
use App\Entity\Product;
use App\Entity\Order;
use App\Entity\Rating;
use App\Form\ProductFormType;
use App\Form\PaymentType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/home', name:'home')]
    public function loadHomePage(Request $request): Response
    {
        return $this->render('homepage.html.twig',);

    }



    #[Route('product/{productId}', )]
    public function loadProductPage(CookieService $cookieService, $productId, Request $request): Response
    {
        $response = new Response();
        $productRepository = $this->entityManager->getRepository(Product::class);
        $targetProduct = $productRepository->findOneBy(['id' => $productId]);

        if ($targetProduct) {

            $cookieService->cookie_update($targetProduct, $request, $response);
            $targetProduct->setViews($targetProduct->getViews() + 1);
            $this->entityManager->persist($targetProduct);
            $this->entityManager->flush();
            $remainingProducts = $targetProduct->getQuantity() - $targetProduct->getItemsSold();
            $form = $this->createForm(PaymentType::class);
            $form->handleRequest($request);

            $errorsList = $form->getErrors(true);

            if ($form->isSubmitted() && $form->isValid()) {

                $mainImagePath = $form->get('cardNumber')->getData();
                $otherImages = $form->get('expMonth')->getData();
                $otherImages = $form->get('expYear')->getData();
                $quantity = $form->get('cvc')->getData();

                //give to stripe the data here and get a response in return

                $stripe_response = json_encode('Successfully purchased ' . $targetProduct->getBrand() . ' ' . $targetProduct->getModel()); // fake stripe's response

                $order = new Order();
                $order->setProduct($targetProduct);
                $order->setUser($this->getUser());
                $order->setPurchaseDate(new \DateTime());
                $order->setPaymentStatus($stripe_response); // fake stripe's response
                $this->entityManager->persist($order);
                $this->entityManager->flush();

                if (str_contains(json_decode($stripe_response), 'Successfull')) {
                    $targetProduct->setItemsSold($targetProduct->getItemsSold() + 1);
                }
                return $this->redirectToRoute('home');
            }
            else{
                return $this->render('product/product_view_page.html.twig', [
                    'form' => $form->createView(),
                    'product' => $targetProduct,
                ], $response);
            }
        }
        else {
            return $this->render('not_found.html.twig', [
                'entity' => "Product"
            ], $response);
        }
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
            'form'=>$form->createView(),
            'errorsList'=>$errorsList,
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
    public function loadUserPage(String $username, Request $request): Response
    {
        $userRepository = $this->entityManager->getRepository(\App\Entity\User::class);
        $targetUser = $userRepository->findOneBy(['username' => $username]);

        if($targetUser){

            $orderRepository = $this -> entityManager -> getRepository(\App\Entity\Order::class);
            $canRate = false;
            if ($orderRepository->alredyBuyer($this->getUser(), $targetUser->getUsername())) {
                $canRate = true;
            }
            if ($canRate) {
                $form = $this->createForm(PaymentType::class);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $rating = new Rating();
                    $rating->setBuyer($this->getUser());
                    $rating->setBuyer($targetUser);
                    $rating->setRatedProduct($form->get('product')->getData());
                    $rating->setRating($form->get('rating')->getData());
                    $rating->setTitle($form->get('title')->getData());
                    $rating->setDescription($form->get('description')->getData());
                    $this->entityManager->persist($rating);
                    $this->entityManager->flush();
                }

                else{
                    return $this->render('user/user_page.html.twig',[
                        'username'=>$username,
                        'isVendor'=>$targetUser->isVendor(),
                        'products'=>$targetUser->getSellingProducts(),
                        'canRate'=>$canRate,
                        'form'=>$form,
                    ]);
                }
            }
            else{
                return $this->render('user/user_page.html.twig',[
                    'username'=>$username,
                    'isVendor'=>$targetUser->isVendor(),
                    'products'=>$targetUser->getSellingProducts(),
                    'canRate'=>$canRate,
                ]);
            }

            return $this->render('user/user_page.html.twig',[
                'username'=>$username,
                'isVendor'=>$targetUser->isVendor(),
                'products'=>$targetUser->getSellingProducts(),
                'canRate'=>$canRate,
            ]);
        }
        else{
            return $this->render('not_found.html.twig',[
                'entity'=>"User"
            ]);
        }
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