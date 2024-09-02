<?php
namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Filesystem;    
use App\Form\ProductFormType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;



class HomeController extends AbstractController
{
    private $entityManager;
    private $filesystem;

    public function __construct(EntityManagerInterface $entityManager, Filesystem $filesystem)
    {
        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
    }

    #[Route('/home', name:'home')]
    public function loadHomePage(): Response
    {
        $data = [];
        $this->filesystem->dumpFile('testFile.json', json_encode(($data)));

        $queryResult = $this -> createQuery('trekking', 'Salomon', 'pink', 1);
        $queryResult2 = $this -> createQuery('trekking', 'Salomon', 'pink', 1);

        if ($queryResult === $queryResult2) {
            // Log or debug here
            dump('Query returned null');
            
            $items = [
                'query' => 'error, null',
            ];

            return $this->render('homePage.html.twig', ['items' => $items]);
        }
        else {
            // Log or debug the actual result
            dump($queryResult);
            dump($queryResult->getColors());
            $string = (string)$queryResult->getColors();
            
            $items = [
                'query' => $string,
            ];
            return $this->render('homePage.html.twig', ['items' => $items]);
        }
    }

    #[Route('product/{productId}', )]
    public function loadProductPage($productId): Response
    {
        $productRepository = $this->entityManager->getRepository(Product::class);
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

    #[Route('/api/fyp-function', name: 'api_fyp_function')]
    public function fyp_function(Request $request): JsonResponse
    {


        // Get 'type' cookies values
        $typeCookie = $request->cookies->get('type');

        $normalizedProbabilities = $this -> normalizeProbabilities($typeCookie);
        // Pick 3 names based on probabilities
        $types = $this -> pickNames($normalizedProbabilities, 4);

        $brandCookie = $request->cookies->get('brand');
        $normalizedProbabilities = $this -> normalizeProbabilities($brandCookie);
        $brands = $this -> pickNames($normalizedProbabilities, 4);

        $colorCookie = $request->cookies->get('color');
        $normalizedProbabilities = $this -> normalizeProbabilities($colorCookie);
        $colors = $this -> pickNames($normalizedProbabilities, 4);
        $data = [];

        $query_history = $this -> read_json_file('testFile.json');

        $queryResult = 1;

        for ($i = 0; $i < 4; $i++) {
            while ($queryResult === null) {

                $v = 0;
                $n = 0;

                foreach ($query_history as $element) {
                    if ($element['query_r'] -> getType() ===  $types[$i] && $element['query_r'] -> getBrand() === $brands[$i] && $element['query_r'] -> getColor() === $colors[$i]) {
                        $n = $element['n'];
                        $element['n'] = $n + 1;
                        $v = 1;
                        break;
                    }
                }

                if ($v === 1) {
                    $queryResult = $this -> createQuery($types[$i], $brands[$i], $colors[$i], $n);
                }
                else {
                    array_push($query_history, [
                                                    'query_r' => $queryResult,
                                                    'n' => 1
                                                ]);
                    $queryResult = $this -> createQuery($types[$i], $brands[$i], $colors[$i], 0);
                }
            }

            $data[$i] = [
                'id' => $queryResult -> getId(),
                'main_image' => $queryResult -> getMainImage(),
                'brand' => $queryResult -> getBrand(),
                'model' => $queryResult -> getModel(),
                'color' => $queryResult -> getColor(),
                'description' => $queryResult -> getDescription(),
            ];
            

            array_push($query_history, $queryResult);

        }

        $this->filesystem->dumpFile('testFile.json', json_encode($query_history));

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


    private function read_json_file($path) {
        $path = $this->getParameter('json_file_path'); // e.g., 'assets/json/data.json'
        $data = file_get_contents($path);
        return json_decode($data, true); // true for associative array
    }
    
}
?>