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
        return $this->render('productpage.html.twig');
    }

    #[Route('createproduct'),]
    public function createProduct(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        
        $form->handleRequest($request);
        //dd($form);
        if($form->isSubmitted() && $form->isValid()){
            
            $newProduct=$form->getData();
            dd($newProduct);
            exit;
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