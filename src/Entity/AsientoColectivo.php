<?php

namespace App\Entity;

use App\Repository\AsientoColectivoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AsientoColectivoRepository::class)]
class AsientoColectivo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'asientoColectivos')]
    private ?Asiento $asiento = null;

    #[ORM\ManyToOne(inversedBy: 'asientoColectivos')]
    private ?Colectivo $colectivo = null;

    /**
     * @var Collection<int, Pasaje>
     */
    #[ORM\OneToMany(targetEntity: Pasaje::class, mappedBy: 'asientoColectivo')]
    private Collection $pasajes;

    public function __construct()
    {
        $this->pasajes = new ArrayCollection();
    }


    public function __toString()
    {
        return $this->asiento->getNumero();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAsiento(): ?Asiento
    {
        return $this->asiento;
    }

    public function setAsiento(?Asiento $asiento): static
    {
        $this->asiento = $asiento;

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
     * @return Collection<int, Pasaje>
     */
    public function getPasajes(): Collection
    {
        return $this->pasajes;
    }

    public function addPasaje(Pasaje $pasaje): static
    {
        if (!$this->pasajes->contains($pasaje)) {
            $this->pasajes->add($pasaje);
            $pasaje->setAsientoColectivo($this);
        }

        return $this;
    }

    public function removePasaje(Pasaje $pasaje): static
    {
        if ($this->pasajes->removeElement($pasaje)) {
            // set the owning side to null (unless already changed)
            if ($pasaje->getAsientoColectivo() === $this) {
                $pasaje->setAsientoColectivo(null);
            }
        }

        return $this;
    }

          
}
