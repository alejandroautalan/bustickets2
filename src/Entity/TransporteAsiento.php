<?php

namespace App\Entity;

use App\Repository\TransporteAsientoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransporteAsientoRepository::class)]
class TransporteAsiento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'asientos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Transporte $transporte = null;

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\Column(nullable: true)]
    private ?int $categoria = null;

    /**
     * @var Collection<int, Boleto>
     */
    #[ORM\OneToMany(targetEntity: Boleto::class, mappedBy: 'asiento')]
    private Collection $boletos;


    public static $categorias = [
        1 => 'Comun',
        2 => 'Semicama',
        3 => 'Cama',
        4 => 'Premium',
    ];

    public static $plantas = [
        0 => 'Baja',
        1 => 'Alta',
    ];

    #[ORM\Column(nullable: true)]
    private ?int $planta = null;

    #[ORM\Column(nullable: true)]
    private ?int $row = null;

    #[ORM\Column(nullable: true)]
    private ?int $col = null;

    public function __construct()
    {
        $this->boletos = new ArrayCollection();
    }

    public static function getCategoriaChoices() {
        return array_flip(self::$categorias);
    }

    public static function getPlantaChoices() {
        return array_flip(self::$plantas);
    }

    public function __toString()
    {
        return 'Asiento N° '.$this->numero;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCategoria(): ?int
    {
        return $this->categoria;
    }

    public function setCategoria(?int $categoria): static
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getPlanta(): ?int
    {
        return $this->planta;
    }

    public function setPlanta(?int $planta): static
    {
        $this->planta = $planta;

        return $this;
    }

    public function getRow(): ?int
    {
        return $this->row;
    }

    public function setRow(?int $row): static
    {
        $this->row = $row;

        return $this;
    }

    public function getCol(): ?int
    {
        return $this->col;
    }

    public function setCol(?int $col): static
    {
        $this->col = $col;

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
            $boleto->setAsiento($this);
        }

        return $this;
    }

    public function removeBoleto(Boleto $boleto): static
    {
        if ($this->boletos->removeElement($boleto)) {
            // set the owning side to null (unless already changed)
            if ($boleto->getAsiento() === $this) {
                $boleto->setAsiento(null);
            }
        }

        return $this;
    }

}
