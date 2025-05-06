<?php

namespace App\Entity;

use App\Repository\ColectivoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ColectivoRepository::class)]
class Colectivo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $patente = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activo = null;

    #[ORM\ManyToOne(inversedBy: 'colectivos')]
    private ?Modelo $modelo = null;

    /**
     * @var Collection<int, AsientoColectivo>
     */
    //#[ORM\OneToMany(targetEntity: AsientoColectivo::class, mappedBy: 'colectivo')]
    //private Collection $asientoColectivos;
    
    /**
     * @var Collection<int, Asiento>
     */
    #[ORM\OneToMany(targetEntity: Asiento::class, mappedBy: 'colectivo')]
    private Collection $asientos;

    /**
     * @var Collection<int, Viaje>
     */
    #[ORM\OneToMany(targetEntity: Viaje::class, mappedBy: 'colectivo')]
    private Collection $viajes;


    public function __construct()
    {
        $this->asientoColectivos = new ArrayCollection();
        $this->viajes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nombre;
    }

    /**
     * @return Collection<int, AsientoColectivo>
     */
    public function getAsientoColectivos(): Collection
    {
        return $this->asientoColectivos;
    }

    public function addAsientoColectivo(AsientoColectivo $asientoColectivo): static
    {
        if (!$this->asientoColectivos->contains($asientoColectivo)) {
            $this->asientoColectivos->add($asientoColectivo);
            $asientoColectivo->setColectivo($this);
        }

        return $this;
    }

    public function removeAsientoColectivo(AsientoColectivo $asientoColectivo): static
    {
        if ($this->asientoColectivos->removeElement($asientoColectivo)) {
            // set the owning side to null (unless already changed)
            if ($asientoColectivo->getColectivo() === $this) {
                $asientoColectivo->setColectivo(null);
            }
        }

        return $this;
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

    public function getPatente(): ?string
    {
        return $this->patente;
    }

    public function setPatente(string $patente): static
    {
        $this->patente = $patente;

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

    public function getModelo(): ?Modelo
    {
        return $this->modelo;
    }

    public function setModelo(?Modelo $modelo): static
    {
        $this->modelo = $modelo;

        return $this;
    }
    
    /**
     * @return Collection<int, Viaje>
     */
    public function getViajes(): Collection
    {
        return $this->viajes;
    }

    public function addViaje(Viaje $viaje): static
    {
        if (!$this->viajes->contains($viaje)) {
            $this->viajes->add($viaje);
            $viaje->setColectivo($this);
        }

        return $this;
    }

    public function removeViaje(Viaje $viaje): static
    {
        if ($this->viajes->removeElement($viaje)) {
            // set the owning side to null (unless already changed)
            if ($viaje->getColectivo() === $this) {
                $viaje->setColectivo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Viaje>
     */
    public function getAsientos(): Collection
    {
        return $this->asientos;
    }

    public function addAsiento(Asiento $asiento): static
    {
        if (!$this->asientos->contains($asiento)) {
            $this->asientos->add($asiento);
            $asiento->setColectivo($this);
        }

        return $this;
    }

    public function removeAsiento(Asiento $asiento): static
    {
        if ($this->viajes->removeElement($asiento)) {
            // set the owning side to null (unless already changed)
            if ($asiento->getColectivo() === $this) {
                $asiento->setColectivo(null);
            }
        }

        return $this;
    }


}
