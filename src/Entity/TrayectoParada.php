<?php

namespace App\Entity;

use App\Repository\TrayectoParadaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrayectoParadaRepository::class)]
class TrayectoParada
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nro_orden = null;

    #[ORM\ManyToOne(inversedBy: 'trayectoParadas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trayecto $trayecto = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Parada $parada = null;

    #[ORM\Column(options: ["unsigned"])]
    private ?int $tipo_parada_id = null;

    public function __toString()
    {
        return 'Punto:'.($this->parada? $this->parada->getNombre(): 'NUEVO');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNroOrden(): ?int
    {
        return $this->nro_orden;
    }

    public function setNroOrden(int $nro_orden): static
    {
        $this->nro_orden = $nro_orden;

        return $this;
    }

    public function getTrayecto(): ?Trayecto
    {
        return $this->trayecto;
    }

    public function setTrayecto(?Trayecto $trayecto): static
    {
        $this->trayecto = $trayecto;

        return $this;
    }

    public function getParada(): ?Parada
    {
        return $this->parada;
    }

    public function setParada(?Parada $parada): static
    {
        $this->parada = $parada;

        return $this;
    }

    public function getTipoParadaId(): ?int
    {
        return $this->tipo_parada_id;
    }

    public function setTipoParadaId(int $tipo_parada_id): static
    {
        $this->tipo_parada_id = $tipo_parada_id;

        return $this;
    }
}
