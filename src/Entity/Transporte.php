<?php

namespace App\Entity;

use App\Repository\TransporteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransporteRepository::class)]
class Transporte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $nombre = null;

    /**
     * @var Collection<int, TransporteAsiento>
     */
    #[ORM\OneToMany(targetEntity: TransporteAsiento::class, mappedBy: 'transporte', orphanRemoval: true)]
    private Collection $asientos;

    #[ORM\Column(nullable: true)]
    private ?int $grilla_rows = null;

    #[ORM\Column(nullable: true)]
    private ?int $grilla_cols = null;

    #[ORM\Column(nullable: true)]
    private ?int $plantas = null;


    public static $planta_choices = [
        0 => 'Planta Baja',
        1 => 'Planta Alta',
    ];

    public function getPlantaLabel($planta){
        return self::$planta_choices[$planta];
    }

    public function __construct()
    {
        $this->asientos = new ArrayCollection();
    }

    public function __toString()
    {
        return ''.$this->nombre;
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
     * @return Collection<int, TransporteAsiento>
     */
    public function getAsientos(): Collection
    {
        return $this->asientos;
    }

    public function addAsiento(TransporteAsiento $asiento): static
    {
        if (!$this->asientos->contains($asiento)) {
            $this->asientos->add($asiento);
            $asiento->setTransporte($this);
        }

        return $this;
    }

    public function removeAsiento(TransporteAsiento $asiento): static
    {
        if ($this->asientos->removeElement($asiento)) {
            // set the owning side to null (unless already changed)
            if ($asiento->getTransporte() === $this) {
                $asiento->setTransporte(null);
            }
        }

        return $this;
    }

    public function getGrillaRows(): ?int
    {
        return $this->grilla_rows;
    }

    public function setGrillaRows(?int $grilla_rows): static
    {
        $this->grilla_rows = $grilla_rows;

        return $this;
    }

    public function getGrillaCols(): ?int
    {
        return $this->grilla_cols;
    }

    public function setGrillaCols(?int $grilla_cols): static
    {
        $this->grilla_cols = $grilla_cols;

        return $this;
    }

    public function getAsientoEnGrilla($planta, $row, $col) {
        $rs = $this->getAsientos()->matching(
            TransporteRepository::createGridPositionCriteria($planta, $row, $col)
        );
        $asiento = null;
        if(!$rs->isEmpty()) {
            $asiento = $rs->first();
        }
        return $asiento;
    }

    public function getPlantas(): ?int
    {
        return $this->plantas;
    }

    public function setPlantas(?int $plantas): static
    {
        $this->plantas = $plantas;

        return $this;
    }
}
