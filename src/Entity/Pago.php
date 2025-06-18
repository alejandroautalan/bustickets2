<?php

namespace App\Entity;

use App\Repository\PagoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PagoRepository::class)]
class Pago
{
    public const PAYMENT_TYPE_UNSPECIFIED = 0;
    public const PAYMENT_TYPE_CASH = 1;
    public const PAYMENT_TYPE_TRANSFER = 2;
    public const PAYMENT_TYPE_MERCADOPAGO = 3;

    public static $tipo_pagos = [
        self::PAYMENT_TYPE_UNSPECIFIED => 'No especificado',
        self::PAYMENT_TYPE_CASH => 'Efectivo',
        self::PAYMENT_TYPE_TRANSFER => 'Transferencia',
        self::PAYMENT_TYPE_MERCADOPAGO => 'MercadoPago',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $monto = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column]
    private ?int $tipo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observacion = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero_comprobante = null;

    #[ORM\Column]
    private ?int $importe_recibido = null;

    #[ORM\ManyToOne(inversedBy: 'pagos')]
    private ?Reserva $reserva = null;

    #[ORM\ManyToOne(inversedBy: 'pagos')]
    private ?User $user = null;

    public function __construct()
    {
        $this->fecha = new \DateTime();
        $this->setTipo(self::PAYMENT_TYPE_MERCADOPAGO);
    }

    public function __toString()
    {
        return (string)$this->id;
    }

    public static function getTipoPagoChoices() {
        return array_flip(self::$tipo_pagos);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonto(): ?int
    {
        return $this->monto;
    }

    public function setMonto(int $monto): static
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

    public function getNumeroComprobante(): ?string
    {
        return $this->numero_comprobante;
    }

    public function setNumeroComprobante(?string $numero_comprobante): static
    {
        $this->numero_comprobante = $numero_comprobante;

        return $this;
    }

    public function getImporteRecibido(): ?int
    {
        return $this->importe_recibido;
    }

    public function setImporteRecibido(int $importe_recibido): static
    {
        $this->importe_recibido = $importe_recibido;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
