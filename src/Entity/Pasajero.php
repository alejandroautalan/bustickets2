<?php

namespace App\Entity;

use App\Repository\PasajeroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PasajeroRepository::class)]
class Pasajero
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellido = null;

    #[ORM\Column]
    private ?int $dni = null;

    #[ORM\Column(nullable: true)]
    private ?int $edad = null;

    #[ORM\Column(length: 10)]
    private ?string $sexo = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha_nacimiento = null;

    /**
     * @var Collection<int, Pasaje>
     */
    #[ORM\OneToMany(targetEntity: Pasaje::class, mappedBy: 'pasajero')]
    private Collection $pasajes;

    public function __construct()
    {
        $this->pasajes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->apellido.', '.$this->nombre;
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

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): static
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getDni(): ?int
    {
        return $this->dni;
    }

    public function setDni(int $dni): static
    {
        $this->dni = $dni;

        return $this;
    }

    public function getEdad(): ?int
    {
        return $this->edad;
    }

    public function setEdad(?int $edad): static
    {
        $this->edad = $edad;

        return $this;
    }

    public function getFechaNacimiento(): ?\DateTimeInterface
    {
        return $this->fecha_nacimiento;
    }

    public function setFechaNacimiento(?\DateTimeInterface $fecha_nacimiento): static
    {
        $this->fecha_nacimiento = $fecha_nacimiento;

        return $this;
    }

    /**
     * @return Collection<int, Pasaje>
     */
    public function getPasajes(): Collection
    {
        return $this->pasajes;
    }

    public function addPasaje(Pasaje $pasaje): static
    {
        if (!$this->pasajes->contains($pasaje)) {
            $this->pasajes->add($pasaje);
            $pasaje->setPasajero($this);
        }

        return $this;
    }

    public function removePasaje(Pasaje $pasaje): static
    {
        if ($this->pasajes->removeElement($pasaje)) {
            // set the owning side to null (unless already changed)
            if ($pasaje->getPasajero() === $this) {
                $pasaje->setPasajero(null);
            }
        }

        return $this;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(string $sexo): static
    {
        $this->sexo = $sexo;

        return $this;
    }

}
