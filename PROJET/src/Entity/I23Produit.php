<?php

namespace App\Entity;

use App\Repository\I23ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: I23ProduitRepository::class)]
class I23Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $prix_unitaire = null;

    #[ORM\Column(type: 'integer')]
    private ?int $quantite_stock = null;

    #[ORM\OneToMany(mappedBy: 'id_produit', targetEntity: I23LignePanier::class, orphanRemoval: true)]
    private Collection $i23LignePaniers;

    public function __construct()
    {
        $this->i23LignePaniers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getPrixUnitaire(): ?float
    {
        return $this->prix_unitaire;
    }

    public function setPrixUnitaire(float $prix_unitaire): self
    {
        $this->prix_unitaire = $prix_unitaire;

        return $this;
    }

    public function getQuantiteStock(): ?int
    {
        return $this->quantite_stock;
    }

    public function setQuantiteStock(int $quantite_stock): self
    {
        $this->quantite_stock = $quantite_stock;

        return $this;
    }

    /**
     * @return Collection<int, I23LignePanier>
     */
    public function getI23LignePaniers(): Collection
    {
        return $this->i23LignePaniers;
    }

    public function addI23LignePanier(I23LignePanier $i23LignePanier): self
    {
        if (!$this->i23LignePaniers->contains($i23LignePanier)) {
            $this->i23LignePaniers->add($i23LignePanier);
            $i23LignePanier->setIdProduit($this);
        }

        return $this;
    }

    public function removeI23LignePanier(I23LignePanier $i23LignePanier): self
    {
        if ($this->i23LignePaniers->removeElement($i23LignePanier)) {
            // set the owning side to null (unless already changed)
            if ($i23LignePanier->getIdProduit() === $this) {
                $i23LignePanier->setIdProduit(null);
            }
        }

        return $this;
    }
}
