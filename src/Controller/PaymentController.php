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
            
            // You would typically process the payment here using Stripe
            Stripe::setApiKey('api_key');

            try {
                $paymentIntent = PaymentIntent::create([
                    'amount' => 5000, // Amount in cents (e.g., $50.00)
                    'currency' => 'usd',
                    'payment_method_types' => ['card'],
                ]);

                // Normally, you'd pass client-side token instead of raw card data
                // For now, just return a success message
                $this->addFlash('success', 'Payment successfully processed!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Payment failed: ' . $e->getMessage());
            }

            return $this->redirectToRoute('payment');
        }

        return $this->render('paymentPage.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
?>