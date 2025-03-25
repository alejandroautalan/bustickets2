<?php

namespace App\Entity;

use App\Repository\ViajeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ViajeRepository::class)]
class Viaje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $origen = null;

    #[ORM\Column(length: 255)]
    private ?string $destino = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $distancia = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $tiempo = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $costo = null;

    #[ORM\Column(nullable: true)]
    private ?bool $estado = null;

    /**
     * @var Collection<int, Pasaje>
     */
    #[ORM\OneToMany(targetEntity: Pasaje::class, mappedBy: 'viaje', orphanRemoval: true)]
    private Collection $pasajes;

    #[ORM\ManyToOne(inversedBy: 'viajes')]
    private ?Colectivo $colectivo = null;


    public function __construct()
    {
        $this->pasajes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->origen.' - '.$this->destino.': '.$this->getFecha()->format('d-m-Y H:m');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrigen(): ?string
    {
        return $this->origen;
    }

    public function setOrigen(string $origen): static
    {
        $this->origen = $origen;

        return $this;
    }

    public function getDestino(): ?string
    {
        return $this->destino;
    }

    public function setDestino(string $destino): static
    {
        $this->destino = $destino;

        return $this;
    }

    public function getDistancia(): ?string
    {
        return $this->distancia;
    }

    public function setDistancia(?string $distancia): static
    {
        $this->distancia = $distancia;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getTiempo(): ?\DateTimeInterface
    {
        return $this->tiempo;
    }

    public function setTiempo(?\DateTimeInterface $tiempo): static
    {
        $this->tiempo = $tiempo;

        return $this;
    }

    public function getCosto(): ?string
    {
        return $this->costo;
    }

    public function setCosto(string $costo): static
    {
        $this->costo = $costo;

        return $this;
    }

    public function isEstado(): ?bool
    {
        return $this->estado;
    }

    public function setEstado(?bool $estado): static
    {
        $this->estado = $estado;

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
            $pasaje->setViaje($this);
        }

        return $this;
    }

    public function removePasaje(Pasaje $pasaje): static
    {
        if ($this->pasajes->removeElement($pasaje)) {
            // set the owning side to null (unless already changed)
            if ($pasaje->getViaje() === $this) {
                $pasaje->setViaje(null);
            }
        }

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
}
