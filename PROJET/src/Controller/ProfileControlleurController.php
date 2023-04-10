<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile', name: 'app_profile_')]
class ProfileControlleurController extends AbstractController
{
    #[Route('/', name: 'controlleur')]
    public function index(): Response
    {
        return $this->render('profile_controlleur/index.html.twig', [
            'controller_name' => 'ProfileControlleurController',
        ]);
    }
}
