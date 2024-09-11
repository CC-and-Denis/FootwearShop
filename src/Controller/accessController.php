<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginAuthenticator;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\HttpFoundation\Cookie;

use Doctrine\ORM\EntityManagerInterface;



class accessController extends AbstractController
{

    public function __construct()
    {
    }

    #[Route('/access/signup',)]
    public function signUp(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator): Response
    {   

        if ($this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $errorsList = $form->getErrors(true);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setVendor(false);
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $type = [
                'trekking' => 1,
                'running' => 0,
                'hiking' => 0,
                'sandals' => 0,
                'heels' => 0,
                'boots' => 0,
                'ankle_boots' => 0,
                'sneakers' => 0,
                'formal' => 0,
                'flip flops' => 0,
                'others' => 0
            ];

            $brand = [
                'Nike' => 0,
                'Adidas' => 0,
                'Puma' => 0,
                'Asics' => 0,
                'Converse' => 0,
                'NewBalance' => 0,
                'Scarpa' => 0,
                'LaSportiva' => 0,
                'Hoka' => 0,
                'Salomon' => 1,
            ];
    
            $color = [
                'white' => 0,
                'yellow' => 0,
                'orange' => 0,
                'red' => 0,
                'green' => 0,
                'blue' => 0,
                'violet' => 0,
                'pink' => 1,
                'cyan' => 0,
                'gray' => 0,
                'black' => 0,
            ];
    

        /*
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        */

            $response = $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

            $typeJSON = json_encode($type);
            $brandJSON = json_encode($brand);
            $colorJSON = json_encode($color);
    
            $response->headers->setCookie(new Cookie('type', $typeJSON, strtotime('2200-01-01 00:00:00')));
            $response->headers->setCookie(new Cookie('brand', $brandJSON, strtotime('2200-01-01 00:00:00')));
            $response->headers->setCookie(new Cookie('color', $colorJSON, strtotime('2200-01-01 00:00:00')));

            return $response; // that include the authentication
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'errorsList'=>$errorsList,
        ]);
        
    }

    #[Route('/access/login')]
    public function login(AuthenticationUtils $authenticationUtils){
            // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if($error){
            $error=$error->getMessage();
        }
        
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        #throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

}
?>