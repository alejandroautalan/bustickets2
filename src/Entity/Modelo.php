<?php

namespace App\Entity;

use App\Repository\ModeloRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModeloRepository::class)]
class Modelo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $version = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activo = null;

    #[ORM\ManyToOne(inversedBy: 'modelos')]
    private ?Marca $marca = null;

    /**
     * @var Collection<int, Colectivo>
     */
    #[ORM\OneToMany(targetEntity: Colectivo::class, mappedBy: 'modelo')]
    private Collection $colectivos;

    public function __construct()
    {
        $this->colectivos = new ArrayCollection();
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

    public function setNombre(?string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function isActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(?bool $activo): static
    {
        $this->activo = $activo;

        return $this;
    }

    public function getMarca(): ?Marca
    {
        return $this->marca;
    }

    public function setMarca(?Marca $marca): static
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * @return Collection<int, Colectivo>
     */
    public function getColectivos(): Collection
    {
        return $this->colectivos;
    }

    public function addColectivo(Colectivo $colectivo): static
    {
        if (!$this->colectivos->contains($colectivo)) {
            $this->colectivos->add($colectivo);
            $colectivo->setModelo($this);
        }

        return $this;
    }

    public function removeColectivo(Colectivo $colectivo): static
    {
        if ($this->colectivos->removeElement($colectivo)) {
            // set the owning side to null (unless already changed)
            if ($colectivo->getModelo() === $this) {
                $colectivo->setModelo(null);
            }
        }

        return $this;
    }
}
