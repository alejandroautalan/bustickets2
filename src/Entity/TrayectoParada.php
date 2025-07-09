<?php

namespace App\Entity;

use App\Repository\TrayectoParadaRepository;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column]
    private ?int $dia = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $hora_llegada = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $hora_partida = null;

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

    public function getDia(): ?int
    {
        return $this->dia;
    }

    public function setDia(int $dia): static
    {
        $this->dia = $dia;

        return $this;
    }

    public function getHoraLlegada(): ?\DateTimeInterface
    {
        return $this->hora_llegada;
    }

    public function setHoraLlegada(?\DateTimeInterface $hora_llegada): static
    {
        $this->hora_llegada = $hora_llegada;

        return $this;
    }

    public function getHoraPartida(): ?\DateTimeInterface
    {
        return $this->hora_partida;
    }

    public function setHoraPartida(?\DateTimeInterface $hora_partida): static
    {
        $this->hora_partida = $hora_partida;

        return $this;
    }
}
