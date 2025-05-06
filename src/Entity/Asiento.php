<?php

namespace App\Entity;

use App\Repository\AsientoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AsientoRepository::class)]
class Asiento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $clase = null;
    
    #[ORM\ManyToOne(inversedBy: 'asientos')]
    private ?Colectivo $colectivo = null;

    /**
     * @var Collection<int, AsientoColectivo>
     */
    //#[ORM\OneToMany(targetEntity: AsientoColectivo::class, mappedBy: 'asiento')]
    //private Collection $asientoColectivos;

    public function __construct()
    {
        $this->asientoColectivos = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->numero;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getClase(): ?string
    {
        return $this->clase;
    }

    public function setClase(?string $clase): static
    {
        $this->clase = $clase;

        return $this;
    }
    
    public function getColectivo(): ?Colectivo
    {
        return $this->colectivo;
    }

    public function setColectivo(?Colectivo $colectivo): static
    {
        $this->colectivo = $colectivo;

        return $this;
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
            $asientoColectivo->setAsiento($this);
        }

        return $this;
    }

    public function removeAsientoColectivo(AsientoColectivo $asientoColectivo): static
    {
        if ($this->asientoColectivos->removeElement($asientoColectivo)) {
            // set the owning side to null (unless already changed)
            if ($asientoColectivo->getAsiento() === $this) {
                $asientoColectivo->setAsiento(null);
            }
        }

        return $this;
    }
}
