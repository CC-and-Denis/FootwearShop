<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Form\ProductFormType;



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
                

            }

            $this->entityManager->persist($newProduct);
            $this->entityManager->flush();

            return $this->redirectToRoute("home");
        } 
        
        return $this->render('editProductPage.html.twig',[
            'form'=>$form->createView(),
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

    
}
?>