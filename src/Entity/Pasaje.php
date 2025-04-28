<?php

namespace App\Entity;

use App\Repository\PasajeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PasajeRepository::class)]
class Pasaje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $estado = null;


    #[ORM\ManyToOne(inversedBy: 'pasajes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Viaje $viaje = null;

    //#[ORM\ManyToOne(inversedBy: 'pasajes')]
    //#[ORM\JoinColumn(nullable: false)]
    //private ?AsientoColectivo $asientoColectivo = null;
    
    #[ORM\ManyToOne()]
    private ?Asiento $asiento = null;

    #[ORM\ManyToOne(inversedBy: 'pasajes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pago $pago = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $costo = null;

    #[ORM\ManyToOne(inversedBy: 'pasajes')]
    private ?Pasajero $pasajero = null;


    public function __construct()
    {
        $this->estado = 0;
    }
    public function __toString()
    {
        return (string)$this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstado(): ?int
    {
        return $this->estado;
    }

    public function setEstado(int $estado): static
    {
        $this->estado = $estado;

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

    public function getViaje(): ?Viaje
    {
        return $this->viaje;
    }

    public function setViaje(?Viaje $viaje): static
    {
        $this->viaje = $viaje;

        return $this;
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
    
//    public function getAsientoColectivo(): ?AsientoColectivo
//    {
//        return $this->asientoColectivo;
//    }
//
//    public function setAsientoColectivo(?AsientoColectivo $asientoColectivo): static
//    {
//        $this->asientoColectivo = $asientoColectivo;
//
//        return $this;
//    }

    public function getPago(): ?Pago
    {
        return $this->pago;
    }

    public function setPago(?Pago $pago): static
    {
        $this->pago = $pago;

        return $this;
    }

    public function getPasajero(): ?Pasajero
    {
        return $this->pasajero;
    }

    public function setPasajero(?Pasajero $pasajero): static
    {
        $this->pasajero = $pasajero;

        return $this;
    }

   

}
