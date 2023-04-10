<?php

namespace App\Controller;

use App\Entity\I23LignePanier;
use App\Entity\I23Panier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\I23PanierRepository;
use App\Repository\I23LignePanierRepository;
use App\Repository\I23ProduitRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\I23Produit;

#[Route('/panier', name: 'app_panier_')]
class PanierController extends AbstractController
{
    
    #[Route('/ajouter', name: 'ajouter')]
    public function ajouterAuPanier(Request $request, I23ProduitRepository $produitRepository, I23LignePanierRepository $lignePanierRepository, I23PanierRepository $panierRepository, EntityManagerInterface $entityManager)
    {
        //Creation d un boolean global a toute la fonction
        $is_remove = false;
        //Recuperer utilisateur connecté
        $user = $this->getUser();
        dump($user);
        //Recuperer le panier de l'utilisateur connecté
        $panier = $panierRepository->findOneBy(['id_utilisateur' => $user]);
        if(!$panier) {
            $panier = new I23Panier();
            $panier->setIdUtilisateur($user);
            $panier->setPrix(0);
            $entityManager->persist($panier);
            $entityManager->flush();
        }
        //Recuperer les lignes de panier de l'utilisateur connecté
        $lignesPanier = $lignePanierRepository->findBy(['id_panier' => $panier]);
        //Recuperer le produit à ajouter au panier
        $produit = $produitRepository->find($request->get('id_produit'));
        //Recuperer la quantité du produit à ajouter au panier
        $quantite = $request->get('quantite');
        //Recuperer la ligne de panier du produit à ajouter au panier
        $lignePanier = $lignePanierRepository->findOneBy(['id_panier' => $panier, 'id_produit' => $produit]);
        //Si la ligne de panier du produit à ajouter au panier existe
        if ($lignePanier) {
            if($lignePanier->getQuantite() + $quantite != 0)
            {
                //Ajouter la quantité du produit à ajouter au panier à la quantité du produit déjà dans le panier
                $lignePanier->setQuantite($lignePanier->getQuantite() + $quantite);
            }
            else
            {
                //Supprimer la ligne de panier du produit à supprimer du panier
                $entityManager->remove($lignePanier);
                $entityManager->flush();
                $is_remove = true;

            }
            
        } else {
            //Sinon, créer une nouvelle ligne de panier pour le produit à ajouter au panier
            $lignePanier = new I23LignePanier();
            $lignePanier->setIdPanier($panier);
            $lignePanier->setIdProduit($produit);
            $lignePanier->setQuantite($quantite);
            dump($lignePanier);
        }
        //Enregistrer la ligne de panier du produit à ajouter au panier
        //$lignePanierRepository->save($lignePanier);
        if(!$is_remove)
        {
            $entityManager->persist($lignePanier);
            $entityManager->flush();
        }

        // modifier le stock du produit
        $findProduit = $produitRepository->findOneBy(['id' => $request->get('id_produit')]);
        $findProduit->setQuantiteStock($findProduit->getQuantiteStock() - $quantite);
        $entityManager->persist($findProduit);
        $entityManager->flush();

        //Retourner à la page des produits
        return $this->redirectToRoute('app_products_liste_produits');
    }

    //Afficher le panier
    #[Route('/afficher', name: 'afficher')]
    public function afficherPanier(I23PanierRepository $panierRepository, I23LignePanierRepository $lignePanierRepository)
    {
        //Recuperer utilisateur connecté
        $user = $this->getUser();
        //Recuperer le panier de l'utilisateur connecté
        $panier = $panierRepository->findOneBy(['id_utilisateur' => $user]);
        //Recuperer les lignes de panier de l'utilisateur connecté
        $lignesPanier = $lignePanierRepository->findBy(['id_panier' => $panier]);
        dump($lignesPanier);
        
        return $this->render('panier/afficher.html.twig', [
            'lignesPanier' => $lignesPanier,
        ]);
    }

    //Supprimer un produit du panier
    #[Route('/supprimer/{produit}/{panier}', name: 'supprimer', defaults: ['produit' => 0, 'panier' => 0])]
    public function supprimerProduitPanier(I23LignePanierRepository $lignePanierRepository, EntityManagerInterface $entityManager, I23ProduitRepository $produitRepo, int $produit = 0, int $panier = 0)
    {
        //Recuperer l'id du produit à supprimer du panier
        $idProduit = $produit;
        //Recuperer l'id du panier
        $idPanier = $panier;
        //Recuperer la ligne de panier du produit à supprimer du panier
        $lignePanier = $lignePanierRepository->findOneBy(['id_panier' => $idPanier, 'id_produit' => $idProduit]);
        //Supprimer la ligne de panier du produit à supprimer du panier
        //$lignePanierRepository->delete($lignePanier);
        $entityManager->remove($lignePanier);
        $entityManager->flush();

        //Mise a jour des stocks des produits correspondants
        $ModifProduit = $produitRepo->findOneBy(['id' => $idProduit]);
        $ModifProduit->setQuantiteStock($ModifProduit->getQuantiteStock() + $lignePanier->getQuantite());
        $entityManager->persist($ModifProduit);
        $entityManager->flush();

        //Retourner à la page du panier
        return $this->redirectToRoute('app_panier_afficher');
    }

    //Vider le panier
    #[Route('/vider', name: 'vider')]
    public function viderPanier(I23PanierRepository $panierRepository, I23LignePanierRepository $lignePanierRepository, EntityManagerInterface $entityManager)
    {
        //Recuperer utilisateur connecté
        $user = $this->getUser();
        //Recuperer le panier de l'utilisateur connecté
        $panier = $panierRepository->findOneBy(['id_utilisateur' => $user]);
        //Recuperer les lignes de panier de l'utilisateur connecté
        $lignesPanier = $lignePanierRepository->findBy(['id_panier' => $panier]);

        //mise a jour des stocks des produits correspondants
        $produitRepo = $entityManager->getRepository(I23Produit::class);
        foreach ($lignesPanier as $lignePanier) {
            $ModifProduit = $produitRepo->findOneBy(['id' => $lignePanier->getIdProduit()->getId()]);
            $ModifProduit->setQuantiteStock($ModifProduit->getQuantiteStock() + $lignePanier->getQuantite());
            $entityManager->persist($ModifProduit);
            $entityManager->flush();
        }

        //Pour chaque ligne de panier de l'utilisateur connecté
        foreach ($lignesPanier as $lignePanier) {
            //Supprimer la ligne de panier
            //$lignePanierRepository->delete($lignePanier);
            $entityManager->remove($lignePanier);
            $entityManager->flush();
        }

        //Retourner à la page du panier
        return $this->redirectToRoute('app_panier_afficher');
    }

    //Passer la commande
    #[Route('/commander', name: 'commander')]
    public function commanderPanier(I23PanierRepository $panierRepository, I23LignePanierRepository $lignePanierRepository, EntityManagerInterface $entityManager)
    {
        //Recuperer utilisateur connecté
        $user = $this->getUser();
        //Recuperer le panier de l'utilisateur connecté
        $panier = $panierRepository->findOneBy(['id_utilisateur' => $user]);
        //Recuperer les lignes de panier de l'utilisateur connecté
        $lignesPanier = $lignePanierRepository->findBy(['id_panier' => $panier]);

        //Pour chaque ligne de panier de l'utilisateur connecté
        foreach ($lignesPanier as $lignePanier) {
            //Supprimer la ligne de panier
            //$lignePanierRepository->delete($lignePanier);
            $entityManager->remove($lignePanier);
            $entityManager->flush();
        }

        //Retourner à la page du panier
        return $this->redirectToRoute('app_panier_afficher');
    }

    //Si on veut faire une action sans route
    // 1 : Créer une méthode sans route
    // 2 : Dans une vue, créer un lien vers cette méthode, on peut meme passer des parametres dans l'action sans route
    // {{ render(controller(’App\\Controller\\Sandbox\\TwigController::palmaresAction’, {n : 3})) }}

    // redirection route
    //return $this->redirectToRoute('sandbox_doctrine_critique_view2', ['id' => $user->getId()]);
}