<?php

namespace App\Entity;

use App\Repository\ProvinciaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProvinciaRepository::class)]
class Provincia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $nombre = null;

    /**
     * @var Collection<int, Ciudad>
     */
    #[ORM\OneToMany(targetEntity: Ciudad::class, mappedBy: 'provincia', orphanRemoval: true)]
    private Collection $ciudades;

    public function __construct()
    {
        $this->ciudades = new ArrayCollection();
    }

    public function __toString()
    {
        return ''.$this->nombre;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @return Collection<int, Ciudad>
     */
    public function getCiudades(): Collection
    {
        return $this->ciudades;
    }

    public function addCiudade(Ciudad $ciudade): static
    {
        if (!$this->ciudades->contains($ciudade)) {
            $this->ciudades->add($ciudade);
            $ciudade->setProvincia($this);
        }

        return $this;
    }

    public function removeCiudade(Ciudad $ciudade): static
    {
        if ($this->ciudades->removeElement($ciudade)) {
            // set the owning side to null (unless already changed)
            if ($ciudade->getProvincia() === $this) {
                $ciudade->setProvincia(null);
            }
        }

        return $this;
    }
}
