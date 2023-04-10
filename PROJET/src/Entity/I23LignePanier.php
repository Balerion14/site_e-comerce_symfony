<?php

namespace App\Entity;

use App\Repository\I23LignePanierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: I23LignePanierRepository::class)]
class I23LignePanier
{
    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'i23LignePaniers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?I23Panier $id_panier = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'i23LignePaniers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?I23Produit $id_produit = null;

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getIdPanier(): ?I23Panier
    {
        return $this->id_panier;
    }

    public function setIdPanier(?I23Panier $id_panier): self
    {
        $this->id_panier = $id_panier;

        return $this;
    }

    public function getIdProduit(): ?I23Produit
    {
        return $this->id_produit;
    }

    public function setIdProduit(?I23Produit $id_produit): self
    {
        $this->id_produit = $id_produit;

        return $this;
    }
}
