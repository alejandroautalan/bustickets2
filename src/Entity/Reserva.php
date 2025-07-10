<?php

namespace App\Entity;

use App\Repository\ReservaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservaRepository::class)]
class Reserva
{
    const STATE_DRAFT = 0;
    const STATE_PENDING_PAYMENT = 1;
    const STATE_COMPLETED = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $estado = null;

    /**
     * @var Collection<int, Boleto>
     */
    #[ORM\OneToMany(targetEntity: Boleto::class, mappedBy: 'reserva')]
    private Collection $boletos;

    #[ORM\ManyToOne]
    private ?Servicio $servicio = null;

    #[ORM\ManyToOne]
    private ?Parada $origen = null;

    #[ORM\ManyToOne]
    private ?Parada $destino = null;

    /**
     * @var Collection<int, Pago>
     */
    #[ORM\OneToMany(targetEntity: Pago::class, mappedBy: 'reserva')]
    private Collection $pagos;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlpago = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $payment_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $preference_id = null;

    #[ORM\ManyToOne(inversedBy: 'reservas')]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $costo = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha_salida = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha_llegada = null;


    public function __construct()
    {
        $this->boletos = new ArrayCollection();
        $this->pagos = new ArrayCollection();
    }

    public function __toString()
    {
        return 'Reserva:'.$this->getId();
    }

    public function getSoloFechaSalida()
    {
        setlocale(LC_TIME, 'es_AR.utf8');
        // Usa strftime con el formato en estilo PHP:
        return strftime('%a %d %b', $this->fecha_salida->getTimestamp());
    }

    public function getSoloHsSalida()
    {
        return $this->fecha_salida->format('H:i');
    }

    public function getSoloFechaLlegada()
    {
        setlocale(LC_TIME, 'es_AR.utf8');
        // Usa strftime con el formato en estilo PHP:
        return strftime('%a %d %b', $this->fecha_llegada->getTimestamp());
    }

    public function getSoloHsLlegada()
    {
        return $this->fecha_llegada->format('H:i');
    }

    public function showBoletosBtn() {
        return $this->estado == self::STATE_PENDING_PAYMENT;
    }

    public function showPaymentBtn() {
        return $this->estado == self::STATE_DRAFT;
    }

    public function showFinalizeBtn() {
        return $this->estado == self::STATE_PENDING_PAYMENT;
    }

    public function recalcularPago() {
        $has_pago = $this->pagos->count() == 1;
        if($has_pago) {
            $total = $this->calcularMontoTotal();
            $pago = $this->pagos[0];
            $pago->setMonto($total);
            $pago->setImporteRecibido($total*0.1);
        }
    }

    public function calcularMontoTotal()
    {
        $total = 0;
        foreach($this->boletos as $boleto) {
            $total = $total + $boleto->getCosto();
        }
        return $total;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstado(): ?int
    {
        return $this->estado;
    }

    public function setEstado(?int $estado): static
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
            $boleto->setCosto($this->getCosto());
            $boleto->setReserva($this);
        }

        return $this;
    }

    public function removeBoleto(Boleto $boleto): static
    {
        if ($this->boletos->removeElement($boleto)) {
            // set the owning side to null (unless already changed)
            if ($boleto->getReserva() === $this) {
                $boleto->setReserva(null);
            }
        }

        return $this;
    }

    public function getServicio(): ?Servicio
    {
        return $this->servicio;
    }

    public function setServicio(?Servicio $servicio): static
    {
        $this->servicio = $servicio;

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

    /**
     * @return Collection<int, Pago>
     */
    public function getPagos(): Collection
    {
        return $this->pagos;
    }

    public function addPago(Pago $pago): static
    {
        if (!$this->pagos->contains($pago)) {
            $this->pagos->add($pago);
            $pago->setReserva($this);
        }

        return $this;
    }

    public function removePago(Pago $pago): static
    {
        if ($this->pagos->removeElement($pago)) {
            // set the owning side to null (unless already changed)
            if ($pago->getReserva() === $this) {
                $pago->setReserva(null);
            }
        }

        return $this;
    }

    public function getUrlpago(): ?string
    {
        return $this->urlpago;
    }

    public function setUrlpago(?string $urlpago): static
    {
        $this->urlpago = $urlpago;

        return $this;
    }

    public function getPaymentId(): ?string
    {
        return $this->payment_id;
    }

    public function setPaymentId(?string $payment_id): static
    {
        $this->payment_id = $payment_id;

        return $this;
    }

    public function getPreferenceId(): ?string
    {
        return $this->preference_id;
    }

    public function setPreferenceId(?string $preference_id): static
    {
        $this->preference_id = $preference_id;

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

    public function getCosto(): ?int
    {
        return $this->costo;
    }

    public function setCosto(int $costo): static
    {
        $this->costo = $costo;

        return $this;
    }

    public function getFechaSalida(): ?\DateTimeInterface
    {
        return $this->fecha_salida;
    }

    public function setFechaSalida(?\DateTimeInterface $fecha_salida): static
    {
        $this->fecha_salida = $fecha_salida;

        return $this;
    }

    public function getFechaLlegada(): ?\DateTimeInterface
    {
        return $this->fecha_llegada;
    }

    public function setFechaLlegada(?\DateTimeInterface $fecha_llegada): static
    {
        $this->fecha_llegada = $fecha_llegada;

        return $this;
    }
    
}
