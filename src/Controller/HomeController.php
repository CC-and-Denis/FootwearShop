<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/home',)]
    public function loadHomePage(): Response
    {
        return $this->render('homePage.html.twig');
    }

    #
}
?>