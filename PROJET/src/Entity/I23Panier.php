<?php

namespace App\Entity;

use App\Repository\I23PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: I23PanierRepository::class)]
class I23Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'i23Panier', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?I23Utilisateur $id_utilisateur = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $prix = null;

    #[ORM\OneToMany(mappedBy: 'id_panier', targetEntity: I23LignePanier::class, orphanRemoval: true)]
    private Collection $i23LignePaniers;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->i23LignePaniers = new ArrayCollection();
    }

    public function getIdUtilisateur(): ?I23Utilisateur
    {
        return $this->id_utilisateur;
    }

    public function setIdUtilisateur(I23Utilisateur $id_utilisateur): self
    {
        $this->id_utilisateur = $id_utilisateur;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

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
            $i23LignePanier->setIdPanier($this);
        }

        return $this;
    }

    public function removeI23LignePanier(I23LignePanier $i23LignePanier): self
    {
        if ($this->i23LignePaniers->removeElement($i23LignePanier)) {
            // set the owning side to null (unless already changed)
            if ($i23LignePanier->getIdPanier() === $this) {
                $i23LignePanier->setIdPanier(null);
            }
        }

        return $this;
    }
}
