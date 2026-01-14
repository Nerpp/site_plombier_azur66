<?php

namespace App\Entity;

use App\Repository\SourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SourceRepository::class)]
class Source
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    /**
     * @var Collection<int, AdminCommentaire>
     */
    #[ORM\OneToMany(targetEntity: AdminCommentaire::class, mappedBy: 'source', orphanRemoval: true)]
    private Collection $admin_commentaire;

    public function __construct()
    {
        $this->admin_commentaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, AdminCommentaire>
     */
    public function getAdminCommentaire(): Collection
    {
        return $this->admin_commentaire;
    }

    public function addAdminCommentaire(AdminCommentaire $adminCommentaire): static
    {
        if (!$this->admin_commentaire->contains($adminCommentaire)) {
            $this->admin_commentaire->add($adminCommentaire);
            $adminCommentaire->setSource($this);
        }

        return $this;
    }

    public function removeAdminCommentaire(AdminCommentaire $adminCommentaire): static
    {
        if ($this->admin_commentaire->removeElement($adminCommentaire)) {
            // set the owning side to null (unless already changed)
            if ($adminCommentaire->getSource() === $this) {
                $adminCommentaire->setSource(null);
            }
        }

        return $this;
    }
}
