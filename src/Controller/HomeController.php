<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;


class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/home', name:'home')]
    public function loadHomePage(): Response
    {
        

        return $this->render('homePage.html.twig',[
          
        ]);
    }

    #[Route('product/{productId}', )]
    public function loadProductPage($productId): Response
    {
        $productRepository = $this->entityManager->getRepository(\App\Entity\Product::class);
        $targetProduct = $productRepository->findOneBy(['id' => $productId]);

        if($targetProduct){
            return $this->render('productpage.html.twig',[
                'product'=>$targetProduct,
            ]
        );
        }else{
            return $this->render('notFound.html.twig',[
                'entity'=>"Product"
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
            
            $newProduct=$form->getData();
            $mainImagePath=$form->get('mainImage')->getData();
            $otherImages=$form->get('otherImages')->getData();
            $quantity = $form->get('quantity')->getData();
            


            if($mainImagePath){
                $newMainIMageName=uniqid().'.'.$mainImagePath->guessExtension();

                try{
                    $mainImagePath->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads',
                        $newMainIMageName
                    );
                }catch(FileException $e){
                    dd($e);
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
        
        return $this->render('editProductPage.html.twig',[
            'form'=>$form->createView()
        ]);
    }


    #[Route('deleteproduct/{id}',name:"delete_product")]
    public function deleteProduct(int $id){
        $productRepository = $this->entityManager->getRepository(\App\Entity\Product::class);
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

    #[Route('editproduct/{id}',name:"edit_product")]
    public function editProduct(int $id){
        $productRepository = $this->entityManager->getRepository(\App\Entity\Product::class);
        $product = $productRepository->findOneBy(['id' => $id]);

        if( (! $product) || $product->getSellerUsername()->getUsername()!=$this->getUser()->getUsername() ){
            $response = new Response("This item can't be deleted",500);
            return $response;
        }
        if( $product->getItemsSold()!=0 ){
            // possibilità solo di aggiungere prodotti e cambiare prezzo
            dd("wip");
        }
        $form = $this->createForm(ProductFormType::class, $product);
        return $this->render('editProductPage.html.twig',[
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
            return $this->render('userPage.html.twig',[
                'username'=>$username,
                'isVendor'=>$targetUser->isVendor(),
                'products'=>$targetUser->getSellingProducts(),
            ]);
        }else{
            return $this->render('notFound.html.twig',[
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

        return $this->render('productCardComponent.html.twig',[
            "products"=>$products,
            'hasMore' => $hasMore,
        ]);
    }




#[Route('/populars',name:"load_popular_products_page")]
public function loadPopularProductsPage(){
    return $this->render('populars.html.twig',[]);
}



}?>