<?php

namespace App\Entity;

use App\Repository\TrayectoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Order;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrayectoRepository::class)]
class Trayecto
{
    const TIPO_PARADA_ORIGEN = 1;
    const TIPO_PARADA_DESTINO = 2;

    public static $tipos_parada = [
        self::TIPO_PARADA_ORIGEN => 'Origen',
        self::TIPO_PARADA_DESTINO => 'Destino',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $nombre = null;

    /**
     * @var Collection<int, TrayectoParada>
     */
    #[ORM\OneToMany(targetEntity: TrayectoParada::class, mappedBy: 'trayecto', orphanRemoval: true, cascade: ["persist"])]
    #[ORM\OrderBy(["nro_orden" => 'ASC'])]
    private Collection $trayectoParadas;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Parada $origen = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Parada $destino = null;

    #[ORM\Column(nullable: true)]
    private ?bool $enabled = null;

    public function __construct()
    {
        $this->trayectoParadas = new ArrayCollection();
    }

    public function __toString()
    {
        return ''.$this->nombre;
    }

    public static function getTiposParadaChoices() {
        return array_flip(self::$tipos_parada);
    }

    public function getParadasByNroOrden(): Collection
    {
        $criteria = new Criteria();
        $criteria->orderBy(['nro_orden' => Order::Ascending]);
        return $this->trayectoParadas->matching($criteria);
    }

    public function getParadasOrigen(): Collection
    {
        $criteria = new Criteria();
        $exp1 = new Comparison('tipo_parada_id', Comparison::EQ, self::TIPO_PARADA_ORIGEN);
        $criteria->where($exp1)
            ->orderBy(['nro_orden' => Order::Ascending]);
        return $this->trayectoParadas->matching($criteria);
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
        //$criteria = new Criteria();
        //$criteria->orderBy(['nro_orden' => Order::Ascending]);
        //return $this->trayectoParadas->matching($criteria);
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

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }
}
