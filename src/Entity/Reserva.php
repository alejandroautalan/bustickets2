<?php

namespace App\Entity;

use App\Repository\ReservaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    public function __construct()
    {
        $this->boletos = new ArrayCollection();
        $this->pagos = new ArrayCollection();
    }

    public function __toString()
    {
        return 'Reserva:'.$this->getId();
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
}
