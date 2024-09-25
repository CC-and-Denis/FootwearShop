<?php

namespace App\Service;

use GuzzleHttp\Client;

class ChatService
{

    public function getResponse(string $message): ?string
{
    $message = strtolower(trim($message));

    // List of predefined responses with associated keywords
    $responses = [
        // Shipping and delivery
        'The shipping takes between 2 to 5 days to be delivered to your home. Shipping costs range between €3.50 and €5.35, depending on your location. If there is any issue, feel free to contact us via the link on our homepage, and we will get back to you as soon as possible.' 
        => ['shipping', 'ship', 'deliver', 'delivered', 'shipment', 'delivery time'],
        
        // Payment methods and details
        'You can securely pay with your credit card via Stripe, our trusted payment platform. We accept most major credit cards.' 
        => ['pay', 'payment', 'credit card', 'stripe', 'method of payment', 'payment options', 'how to pay'],

        // Order processing time
        'Your order will be processed within 24 hours after payment confirmation (if the payment is successful). You will receive a notification once your order is shipped.' 
        => ['order', 'orders', 'process', 'processed', 'order time', 'how long to process'],

        // Refund and returns
        'Refunds are possible through the Stripe platform. If you need to initiate a return or refund, please contact our support team, and they will guide you through the process.' 
        => ['refund', 'refunds', 'return', 'return policy', 'how to refund'],

        // Selling used products
        'You can list your used products for sale by creating an account and following the step-by-step guide in our "Sell Your Products" section. It\'s easy to start selling!' 
        => ['sell', 'selling', 'used product', 'how to sell', 'sell my product', 'used', 'second hand'],

        // Product availability
        'If a product is out of stock, you can opt to be notified by email when it becomes available again. Simply click "Notify Me" on the product page.' 
        => ['availability', 'in stock', 'out of stock', 'available', 'product availability', 'product stock'],

        // Account-related queries
        'To login you can click the image on the bottom part of sidebar' 
        => ['account', 'login', 'log in', 'password', 'reset password', 'forgot password', 'how to log in', 'sign in'],

        // Contact support
        'If you need any help, you can always reach out to our customer support through the "Contact Us" link on our website. We\'ll get back to you as soon as possible.' 
        => ['contact', 'support', 'customer service', 'help', 'contact us', 'get in touch'],

        'Tou don\'t have to use this words!!!'
        => ['child', 'children', 'nigg','nigga', 'nigger', 'I\'m gonna touch you', 'oil up'],
    ];

    // Check for keywords in the message
    $matches = [];
    foreach ($responses as $response => $keys) {
        foreach ($keys as $key) {
            // Look for whole-word matches using a regular expression
            if (preg_match('/\b' . preg_quote($key, '/') . '\b/', $message)) {
                $matches[] = $response;
                break; // No need to check more keywords if one matches
            }
        }
    }

    if (count($matches) > 0) {
        return implode("\n\n", $matches);
    }

    // If no match is found, suggest possible topics
    return 'Sorry, I couldn\'t find a suitable answer to your question. Try asking about: shipping, payment, orders, refunds, selling products, product availability, or contact support for more help.';
}

}

?>