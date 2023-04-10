<?php

namespace App\Controller;

use App\Repository\I23LignePanierRepository;
use App\Repository\I23PanierRepository;
use App\Repository\I23UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestion/user', name: 'app_gestion_user_')]
class GestionUserController extends AbstractController
{
    #[Route('/affichage', name: 'affichage')]
    public function index(I23UtilisateurRepository $user): Response
    {
        //recup de la liste des utilisateurs
        $listeUser = $user->findAll();
        return $this->render('gestion_user/index.html.twig', [
            'listeUser' => $listeUser,
        ]);
    }

    //suppression d'un utilisateur
    #[Route('/suppression/{id}', name: 'suppression')]
    public function suppression(EntityManagerInterface $manageUser, I23UtilisateurRepository $user, I23PanierRepository $i23PanierRepository, I23LignePanierRepository $i23LignePanierRepository, int $id): Response
    {
        /*Il est possible de supprimer un utilisateur (attention `a vider proprement son panier s’il a des commandes en cours) */
        //recup de l'utilisateur
        $user = $user->find($id);
        //Verification que c est pas un super admin et l utilisateur courant
        if($user->getRoles()[0] == 'USER_SUPER_ADMIN'){
            return $this->redirectToRoute('app_gestion_user_affichage');
        }
        //recup du panier de l'utilisateur recupere
        $panier = $i23PanierRepository->findOneBy(['id_utilisateur' => $user]);
        //recup des lignes de panier du panier de l'utilisateur recupere
        $lignesPanier = $i23LignePanierRepository->findBy(['id_panier' => $panier]);
        //suppression des lignes de panier
        foreach ($lignesPanier as $lignePanier) {
            //Replacer le stock de du produit dans la table produit
            $produit = $lignePanier->getIdProduit();
            $stock = $produit->getQuantiteStock();
            $stock += $lignePanier->getQuantite();
            $produit->setQuantiteStock($stock);
            $manageUser->persist($produit);
            $manageUser->flush();
            //suppression de la ligne de panier
            $manageUser->remove($lignePanier);
            $manageUser->flush();
        }
        //suppression du panier
        if($panier != null)
        {
            $manageUser->remove($panier);
            $manageUser->flush();
        }
        //suppression de l'utilisateur
        $manageUser->remove($user);
        $manageUser->flush();
        return $this->redirectToRoute('app_gestion_user_affichage');
    }
}
