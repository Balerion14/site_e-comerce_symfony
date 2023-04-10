<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MessageGenerator;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(MessageGenerator $messageGenerator): Response
    {
        $message = $messageGenerator->getHappyMessage();
        $this->addFlash('service_message_generator', $message);
        return $this->render('main/index.html.twig');
    }
}
