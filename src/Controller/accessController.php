<?php
namespace App\Controller;

use App\Service\CookieService;
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
    public function signUp(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator, CookieService $cookieService): Response
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
            $cookies = $cookieService -> cookie_creation();

            $typeJSON = json_encode($cookies['type']);
            $brandJSON = json_encode($cookies['brand']);
            $colorJSON = json_encode($cookies['color']);
    
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