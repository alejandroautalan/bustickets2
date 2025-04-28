<?php

namespace App\Entity;

use App\Admin\PasajeAdmin;
use App\Controller\PasajeAdminController;
use App\Repository\PagoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PagoRepository::class)]
class Pago
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $monto = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column]
    private ?int $tipo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observacion = null;

    #[ORM\Column(nullable: true)]
    private ?int $usuario = null;

    /**
     * @var Collection<int, Pasaje>
     */
    #[ORM\OneToMany(targetEntity: Pasaje::class, mappedBy: 'pago', orphanRemoval: true, cascade: ['persist'])]
    private Collection $pasajes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero_comprobante = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $importe_recibido = null;

    #[ORM\ManyToOne(inversedBy: 'pagos')]
    private ?Reserva $reserva = null;

    public function __construct()
    {
        $this->pasajes = new ArrayCollection();
        $this->fecha = new \DateTime();
    }

    public function __toString()
    {
        return (string)$this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonto(): ?string
    {
        return $this->monto;
    }

    public function setMonto(string $monto): static
    {
        $this->monto = $monto;

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

    public function getTipo(): ?int
    {
        return $this->tipo;
    }

    public function setTipo(int $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getObservacion(): ?string
    {
        return $this->observacion;
    }

    public function setObservacion(?string $observacion): static
    {
        $this->observacion = $observacion;

        return $this;
    }

    public function getUsuario(): ?int
    {
        return $this->usuario;
    }

    public function setUsuario(?int $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getNumeroComprobante(): ?string
    {
        return $this->numero_comprobante;
    }

    public function setNumeroComprobante(?string $numero_comprobante): static
    {
        $this->numero_comprobante = $numero_comprobante;

        return $this;
    }

    public function getImporteRecibido(): ?string
    {
        return $this->importe_recibido;
    }

    public function setImporteRecibido(string $importe_recibido): static
    {
        $this->importe_recibido = $importe_recibido;

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
            $pasaje->setPago($this);
        }

        return $this;
    }

    public function removePasaje(Pasaje $pasaje): static
    {
        if ($this->pasajes->removeElement($pasaje)) {
            // set the owning side to null (unless already changed)
            if ($pasaje->getPago() === $this) {
                $pasaje->setPago(null);
            }
        }

        return $this;
    }

    public function getReserva(): ?Reserva
    {
        return $this->reserva;
    }

    public function setReserva(?Reserva $reserva): static
    {
        $this->reserva = $reserva;

        return $this;
    }
    
   
}
