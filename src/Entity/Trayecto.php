<?php

namespace App\Entity;

use App\Repository\TrayectoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrayectoRepository::class)]
class Trayecto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $nombre = null;

    /**
     * @var Collection<int, TrayectoParada>
     */
    #[ORM\OneToMany(targetEntity: TrayectoParada::class, mappedBy: 'trayecto', orphanRemoval: true)]
    private Collection $trayectoParadas;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Parada $origen = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Parada $destino = null;

    public function __construct()
    {
        $this->trayectoParadas = new ArrayCollection();
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
     * @return Collection<int, TrayectoParada>
     */
    public function getTrayectoParadas(): Collection
    {
        return $this->trayectoParadas;
    }

    public function addTrayectoParada(TrayectoParada $trayectoParada): static
    {
        if (!$this->trayectoParadas->contains($trayectoParada)) {
            $this->trayectoParadas->add($trayectoParada);
            $trayectoParada->setTrayecto($this);
        }

        return $this;
    }

    public function removeTrayectoParada(TrayectoParada $trayectoParada): static
    {
        if ($this->trayectoParadas->removeElement($trayectoParada)) {
            // set the owning side to null (unless already changed)
            if ($trayectoParada->getTrayecto() === $this) {
                $trayectoParada->setTrayecto(null);
            }
        }

        return $this;
    }

    public function getOrigen(): ?Parada
    {
        return $this->origen;
    }

    public function setOrigen(?Parada $origen): static
    {
        $this->origen = $origen;

        return $this;
    }

    public function getDestino(): ?Parada
    {
        return $this->destino;
    }

    public function setDestino(?Parada $destino): static
    {
        $this->destino = $destino;

        return $this;
    }
}
