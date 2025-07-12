<?php

namespace App\Entity;

use App\Repository\ServicioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ServicioRepository::class)]
class Servicio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'La fecha no puede estar vacía')]
    #[Assert\GreaterThanOrEqual(
        value: 'today',
        message: 'La fecha no puede ser anterior a hoy.'
    )]
    private ?\DateTimeInterface $partida = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'La fecha no puede estar vacía')]
    #[Assert\GreaterThanOrEqual(
        value: 'today',
        message: 'La fecha no puede ser anterior a hoy.'
    )]
    private ?\DateTimeInterface $llegada = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trayecto $trayecto = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Transporte $transporte = null;

    #[ORM\ManyToOne]
    private ?Vehiculo $vehiculo = null;

    #[ORM\Column]
    private ?int $estado = null;

    #[ORM\Column(nullable: true)]
    private ?int $costo = null;

    ####para filter
    #[ORM\Column(nullable: true)]
    private ?int $origen = null;

    #[ORM\Column(nullable: true)]
    private ?int $destino = null;


    #[Assert\Callback]
    public function validateFechas(ExecutionContextInterface $context, $payload)
    {
        if ($this->llegada && $this->partida) {
            if ($this->partida > $this->llegada) {
                $context->buildViolation('La fecha de partida no puede ser mayor que la fecha de llegada.')
                    ->atPath('partida')
                    ->addViolation();
            }
        }
    }

    const STATE_DRAFT = 1;
    const STATE_PROGRAMMED = 2;
    const STATE_TRANSPORTING = 3;
    const STATE_FINISHED = 4;

    public static $estado_choices = [
        'Draft' => self::STATE_DRAFT,
        'Programado' => self::STATE_PROGRAMMED,
        'Transporte' => self::STATE_TRANSPORTING,
        'Finalizado'=> self::STATE_FINISHED
    ];

    public static $estado_nombre_choices = [
        self::STATE_DRAFT        => 'Draft',
        self::STATE_PROGRAMMED   => 'Programado',
        self::STATE_TRANSPORTING => 'Transporte',
        self::STATE_FINISHED     => 'Finalizado'
    ];
    /**
     * @var Collection<int, Boleto>
     */
    #[ORM\OneToMany(targetEntity: Boleto::class, mappedBy: 'servicio')]
    private Collection $boletos;

    public function getOrigenDestinoTrayecto($od)
    {
        $parada_nombre = null;
        foreach ($this->getTrayecto()->getTrayectoParadas() as $tp):
            if ($tp->getParada()->getId() == $od):
                $parada_nombre = $tp->getParada()->getNombre();
            endif;
        endforeach;
        return $parada_nombre;
    }

    public function getOrigenDestinoTrayectoDias($od)
    {
        $parada_dia = 0;
        foreach ($this->getTrayecto()->getTrayectoParadas() as $tp):
            if ($tp->getParada()->getId() == $od):
                    $parada_dia = $tp->getDia();
            endif;
        endforeach;
        return '+'.$parada_dia.' day';
    }

    public function getOrigenDestinoTrayectoHs($od)
    {
        $parada_hs = null;
        foreach ($this->getTrayecto()->getTrayectoParadas() as $tp):
            if ($tp->getParada()->getId() == $od):
                if($tp->getTipoParadaId() == 1):
                    $parada_hs = $tp->getHoraPartida();
                else:
                    $parada_hs = $tp->getHoraLlegada();
                endif;
            endif;
        endforeach;
        return $parada_hs;
    }
    public function getAsientosLibres()
    {
         $a = 0;
         $b = 0;
         foreach ($this->transporte->getAsientos() as $asiento):
                 $a++;
         endforeach;
        foreach ($this->boletos as $boleto):
            if($boleto->getEstado() == 3 or $boleto->getEstado() == 1):
                $b++;
            endif;
        endforeach;
         return $a-$b;
    }
    public function __construct()
    {
        $this->boletos = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->trayecto->getOrigen().' > '.$this->trayecto->getDestino() ;
    }

    public function getFecha(): ?\DateTimeInterface {
        return $this->getPartida();
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->recalcularFechasOrigenDestino($fecha);

        return $this;
    }

    public function recalcularFechasOrigenDestino(\DateTimeInterface $fecha) {
        $trayecto = $this->getTrayecto();
        $paradas = $trayecto->getParadasByNroOrden();
        $tp_origen = $paradas->first();
        $tp_destino = $paradas->last();

        $origen_dia = $tp_origen->getDia();
        $origen_hora = $tp_origen->getHoraPartida();

        $destino_dia = $tp_destino->getDia();
        $destino_hora = $tp_destino->getHoraLLegada();

        $forigen = clone $fecha;
        $fdestino = clone $fecha;

        if($origen_hora !== null) {
            $interval_origen = sprintf(
                'P%sDT%sH%sM', $origen_dia, $origen_hora->format('H'), $origen_hora->format('i'));
            $forigen->add(new \DateInterval($interval_origen));
        }
        if($destino_hora !== null) {
            $interval_destino = sprintf(
                'P%sDT%sH%sM', $destino_dia, $destino_hora->format('H'), $destino_hora->format('i'));
            $fdestino->add(new \DateInterval($interval_destino));
        }
        $this->setPartida($forigen);
        $this->setLlegada($fdestino);
    }

    public function getNombreTrayecto()
    {
        return $this->trayecto->getOrigen().' > '.$this->trayecto->getDestino() ;
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

    public function getPartida(): ?\DateTimeInterface
    {
        return $this->partida;
    }

    public function setPartida(\DateTimeInterface $partida): static
    {
        $this->partida = $partida;

        return $this;
    }

    public function getLlegada(): ?\DateTimeInterface
    {
        return $this->llegada;
    }

    public function setLlegada(\DateTimeInterface $llegada): static
    {
        $this->llegada = $llegada;

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

    public function getTransporte(): ?Transporte
    {
        return $this->transporte;
    }

    public function setTransporte(?Transporte $transporte): static
    {
        $this->transporte = $transporte;

        return $this;
    }

    public function getVehiculo(): ?Vehiculo
    {
        return $this->vehiculo;
    }

    public function setVehiculo(?Vehiculo $vehiculo): static
    {
        $this->vehiculo = $vehiculo;

        return $this;
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

    /**
     * @return Collection<int, Boleto>
     */
    public function getBoletos(): Collection
    {
        return $this->boletos;
    }

    public function addBoleto(Boleto $boleto): static
    {
        if (!$this->boletos->contains($boleto)) {
            $this->boletos->add($boleto);
            $boleto->setServicio($this);
        }

        return $this;
    }

    public function removeBoleto(Boleto $boleto): static
    {
        if ($this->boletos->removeElement($boleto)) {
            // set the owning side to null (unless already changed)
            if ($boleto->getServicio() === $this) {
                $boleto->setServicio(null);
            }
        }

        return $this;
    }

    public function getCosto(): ?int
    {
        return $this->costo;
    }

    public function setCosto(?int $costo): static
    {
        $this->costo = $costo;

        return $this;
    }

    public function getTrayectoD(): ?Trayecto
    {
        return $this->trayectoD;
    }

    public function setTrayectoD(?Trayecto $trayectoD): static
    {
        $this->trayectoD = $trayectoD;

        return $this;
    }

    public function getOrigen(): ?int
    {
        return $this->origen;
    }

    public function setOrigen(?int $origen): static
    {
        $this->origen = $origen;

        return $this;
    }

    public function getDestino(): ?int
    {
        return $this->destino;
    }

    public function setDestino(?int $destino): static
    {
        $this->destino = $destino;

        return $this;
    }
}
