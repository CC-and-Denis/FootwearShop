<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends AbstractController
{
    #[Route('/login',)]
    public function loadLoginPage(): Response
    {
        return $this->render('loginOrSignUpPage.html');
    }

    #[Route('/login/switchmode',)]
    public function switchMode(Request $request): Response{
        $data = json_decode($request->getContent(), true);

        // Now you can access the data like this:
        $value = $data['mode'];
        if($value){
            return $this->render('signUpMenuComponent.html');
        }
        return $this->render('loginMenuComponent.html');
        
    }
    #
}
?>