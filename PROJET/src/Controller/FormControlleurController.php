<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\I23Utilisateur;
use App\Entity\I23Produit;
use App\Form\RegistrationFormType;
use App\Form\I23ProduitType;
use App\Form\I23UtilisateurType;
use App\Repository\I23ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


//Attention, probleme, modification mot de passe ne marche pas?
#[Route('/form/controlleur', name: 'form_controlleur_')]
class FormControlleurController extends AbstractController
{
    //Gestion compte(utilisateur)
    #[Route('/gestion_compte_user', name: 'gestion_compte_user')]
    public function gestion_compte_user(EntityManagerInterface $EntityManage, Request $request): Response
    {
        //Recuperation de l'utilisateur connecté
        $user = $this->getUser();
        //Recuperation de l'utilisateur a modifier
        $user = $EntityManage->getRepository(I23Utilisateur::class)->find($user);
        //Verif si il existe
        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$user
            );
        }
        //on cree le formulaire en remplissant les champs avec les donn´ees du film r´ecup´er´e dans la base
        $form = $this->createForm(RegistrationFormType::class, $user);
        // on lui ajoute le bouton submit
        $form->add('send', SubmitType::class, ['label' => 'Edit user']);
        // on recupere les donnees du formulaire
        /*le point important est que si un formulaire est recu,
        ses donnees sont recopiees dans $user, en ecrasant les donnees lues dans la base de donnees. */
        $form->handleRequest($request);

        //permet de verifier que l’on a bien re¸cu un formulaire (i.e. que ce n’est pas l’appel initial
        //de l’action) et qu’il est correctement rempli (nous d´etaillerons cette notion un peu plus tard).
        if ($form->isSubmitted() && $form->isValid())
        {
            /*l’objet a ete automatiquement hydrate avec les donnees du formulaire (appel a handleRequest). En outre il a ete recupere par le repository donc il est deja persiste. Il reste juste a faire
            un flush pour mettre la base de donnees a jour */
            $EntityManage->flush();
            //comme il y a une redirection apres, un message flash pour indiquer la reussite de l’edition.
            $this->addFlash('info_edit_ok', 'édition user réussie');
            //il ne faut pas rester dans cette action, sinon on reaffiche le formulaire  une redirection s’impose.
            if($this->getUser()->getRoles()[0] == 'ROLE_SUPER_ADMIN')
            {
                return $this->redirectToRoute('app_main');
            }
            else
            {
                return $this->redirectToRoute('app_products_liste_produits');
            }
        }

        if ($form->isSubmitted())
            $this->addFlash('info_edit', 'formulaire user incorrect');

        $args = array(
            'myform' => $form->createView(),
        );
        return $this->render('form_controlleur/index.html.twig', $args);
    }

    //Ajout produit
    #[Route('/ajout_produit', name: 'ajout_produit')]
    public function addProduit(Request $request, I23ProduitRepository $i23ProduitRepository, EntityManagerInterface $entityManagerInterface) : Response
    {
        
        $produit = new I23Produit();
        $form = $this->createForm(I23ProduitType::class, $produit);
        $form->add('send', SubmitType::class, ['label' => 'Ajouter produit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManagerInterface->persist($produit);
            $entityManagerInterface->flush();
            $this->addFlash('info_add_ok', 'ajout produit réussie');
            return $this->redirectToRoute('app_products_liste_produits');
        }

        if ($form->isSubmitted())
            $this->addFlash('info_add', 'formulaire produit incorrect');

        $args = array(
            'myform' => $form->createView(),
        );
        return $this->render('form_controlleur/add_produit.html.twig', $args);
    }

    //Ajout d'un USER_ADMIN
    #[Route('/ajout_user_admin', name: 'ajout_user_admin')]
    public function addUserAdmin(UserPasswordHasherInterface $userPasswordHasher, Request $request, EntityManagerInterface $entityManagerInterface) : Response
    {
        $user = new I23Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->add('send', SubmitType::class, ['label' => 'Ajouter user admin']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_ADMIN']);
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
            $this->addFlash('info_add_admin_ok', 'ajout user admin réussie');
            return $this->redirectToRoute('app_main');
        }

        if ($form->isSubmitted())
            $this->addFlash('info_add_admin', 'formulaire user admin incorrect');

        $args = array(
            'myform' => $form->createView(),
        );
        return $this->render('form_controlleur/add_user_admin.html.twig', $args);
    }
}

//Exemple de contrainte a appliquer dans les entites
/*  #[ORM\Column(length: 200)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 200,
        maxMessage: 'La taille du titre est trop grande ; la limite est {{ limit }}',
    )]
    private ?string $titre = null;

    #[ORM\Column(options: ['comment' => 'année de sortie'])]
    // le paramètre "name" n'est pas précisé, le nom du champ sera celui du membre : "annee"
    // le paramètre "type" n'est pas précisé, ce sera celui correspondant à 'int' : "integer"
    #[Assert\Range(
        minMessage: 'Avant {{ limit }} le cinéma n\'existait pas',
        min: 1850,
    )]
    #[Assert\Range(
        maxMessage: '{{ value }} est trop loin dans le futur, après {{ limit }} ...',
        max: 2053,
    )]
    private ?int $annee = null;

    #[ORM\Column(name: 'enstock', type: 'boolean', options: ['default' => true])]
    // paramètre "name" inutile ici car c'est déjà la valeur par défaut (c'est pour l'exemple)
    // idem pour le paramètre "type"
    #[Assert\Type(
        type: 'boolean',
        message: '{{ value }} n\'est pas de type {{ type }}',
    )]
    private ?bool $enstock = null;

    #[ORM\Column]
    #[Assert\Range(
        notInRangeMessage: 'le prix doit être compris entre {{ min }} et {{ max }}',
        min: 1,
        max: 9999.99,
    )]
    private ?float $prix = null;
*/

// Affichage du formulaire
/*{{ form_start(myform) }}
11
12 {{ form_errors(myform) }}
13
14 {{ form_row(myform.titre) }}
15
16 <div>
17 {{ form_label(myform.annee) }}
18 {{ form_errors(myform.annee) }}
19 {{ form_widget(myform.annee) }}
20 </div>
21
22 {{ form_rest(myform) }}
23
24 {{ form_end(myform) }}
✝ ✆
Explications :
- ligne 10 : affiche la balise <form> avec ses attributs (action, enctype, ...)
- ligne 12 : affiche les erreurs globales (i.e. non reli´ees `a un champ)
- ligne 14 : affiche le champ de saisie complet pour le titre du film : label, messages d’erreur (si
pr´esents), widget
- lignes 16 `a 20 : code ´equivalent `a la ligne 14 pour le champ annee
- ligne 22 : affiche tous les champs non encore affich´es
- ligne 24 : affiche tous les champs non encore affich´es ainsi que la balise fermante </form> */
