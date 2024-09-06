<?php
namespace App\Controller;

use App\Form\PaymentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends AbstractController
{
    #[Route('/payment',)]
    public function loadPaymentPage(Request $request): Response
    {
        // Create the form
        $form = $this->createForm(PaymentType::class);

        // Handle form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get form data
            $data = $form->getData();
            
            // For debugging: dump the data and exit
            dd($data);  // This will show the form data

            // Process the payment here (e.g., using Stripe or another gateway)

            return $this->redirectToRoute('payment');
        }

        return $this->render('paymentPage.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
?>