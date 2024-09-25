<?php

namespace App\Controller;

use App\Service\ChatService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class chatController extends AbstractController
{
    private $chatBot;

    public function __construct(ChatService $chatBot)
    {
        $this->chatBot = $chatBot;
    }


    #[Route('/chatbot', name: 'chatbot', methods: ['POST'])]
    public function chatbot(Request $request): JsonResponse
    {
        $userMessage = json_decode($request->getContent(), true);

        // Example response based on user message (you can use your logic or AI integration here)
        $responseMessage = $this->chatBot->getResponse($userMessage);

        return new JsonResponse(['response' => $responseMessage], 200);
    }

}

?>