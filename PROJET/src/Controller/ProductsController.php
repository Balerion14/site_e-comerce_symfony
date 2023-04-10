<?php

namespace App\Controller;

use App\Repository\I23ProduitRepository;
use App\Repository\I23PanierRepository;
use App\Repository\I23LignePanierRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products', name: 'app_products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'liste_produits')]
    public function index(I23ProduitRepository $product, I23PanierRepository $i23PanierRepository, I23LignePanierRepository $i23LignePanierRepository): Response
    {
        //Recuperer utilisateur connecté
        $user = $this->getUser();
        //Recuperer le panier de l'utilisateur connecté
        $panier = $i23PanierRepository->findOneBy(['id_utilisateur' => $user]);
        //Recuperer les lignes de panier de l'utilisateur connecté
        $lignesPanier = $i23LignePanierRepository->findBy(['id_panier' => $panier]);

        return $this->render('products/index.html.twig', [
            'produits' => $product->findBy([], ['id' => 'asc']),
            'lignesPanier' => $lignesPanier,
        ]);
    }
}
