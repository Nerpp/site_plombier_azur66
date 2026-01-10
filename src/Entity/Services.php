<?php

namespace App\Entity;

use App\Repository\ServicesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServicesRepository::class)]
class Services
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, PhotoServices>
     */
    #[ORM\OneToMany(targetEntity: PhotoServices::class, mappedBy: 'services', cascade: ['persist','remove'],orphanRemoval: true)]
    private Collection $photoServices;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Admin $admin_service = null;

    public function __construct()
    {
        $this->photoServices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, PhotoServices>
     */
    public function getPhotoServices(): Collection
    {
        return $this->photoServices;
    }

    public function addPhotoService(PhotoServices $photoService): static
    {
        if (!$this->photoServices->contains($photoService)) {
            $this->photoServices->add($photoService);
            $photoService->setServices($this);
        }

        return $this;
    }

    public function removePhotoService(PhotoServices $photoService): static
    {
        if ($this->photoServices->removeElement($photoService)) {
            // set the owning side to null (unless already changed)
            if ($photoService->getServices() === $this) {
                $photoService->setServices(null);
            }
        }

        return $this;
    }

    public function getAdminService(): ?Admin
    {
        return $this->admin_service;
    }

    public function setAdminService(?Admin $admin_service): static
    {
        $this->admin_service = $admin_service;

        return $this;
    }
}
