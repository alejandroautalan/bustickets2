<?php

namespace App\Entity;

use App\Repository\ConfigPrecioRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\TransporteAsiento;


#[ORM\Entity(repositoryClass: ConfigPrecioRepository::class)]
class ConfigPrecio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ["unsigned"])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Provincia $origen_provincia = null;

    #[ORM\ManyToOne]
    private ?Ciudad $origen_ciudad = null;

    #[ORM\ManyToOne]
    private ?Parada $origen_parada = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Provincia $destino_provincia = null;

    #[ORM\ManyToOne]
    private ?Ciudad $destino_ciudad = null;

    #[ORM\ManyToOne]
    private ?Parada $destino_parada = null;

    #[ORM\Column(nullable: true)]
    private ?int $categoria_id = null;

    #[ORM\Column]
    private ?int $costo = null;

    public function __toString()
    {
        return 'ConfigPrecio:'.$this->getId();
    }

    public static function getCategorias() {
        $categorias = [0 => 'Sin Especificar'];
        $categorias = array_replace($categorias, TransporteAsiento::$categorias);
        return $categorias;
    }

    public static function getCategoriaChoices() {
        $categorias = self::getCategorias();
        return array_flip($categorias);
    }

    public function getOrigenAsLabel() {
        $label = '';
        $provincia = $this->getOrigenProvincia()->getNombre();
        $ciudad = ($this->getOrigenCiudad())? '' . $this->getOrigenCiudad()->getNombre() . ', ': '';
        $parada = ($this->getOrigenParada())? '' . $this->getOrigenParada()->getNombre() . ', ': '';
        $label = sprintf("%s%s%s", $parada, $ciudad, $provincia);
        return $label;
    }

    public function getDestinoAsLabel() {
        $label = '';
        $provincia = $this->getDestinoProvincia()->getNombre();
        $ciudad = ($this->getDestinoCiudad())? '' . $this->getDestinoCiudad()->getNombre() . ', ': '';
        $parada = ($this->getDestinoParada())? '' . $this->getDestinoParada()->getNombre() .', ': '';
        $label = sprintf("%s%s%s", $parada, $ciudad, $provincia);
        return $label;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrigenProvincia(): ?Provincia
    {
        return $this->origen_provincia;
    }

    public function setOrigenProvincia(?Provincia $origen_provincia): static
    {
        $this->origen_provincia = $origen_provincia;

        return $this;
    }

    public function getOrigenCiudad(): ?Ciudad
    {
        return $this->origen_ciudad;
    }

    public function setOrigenCiudad(?Ciudad $origen_ciudad): static
    {
        $this->origen_ciudad = $origen_ciudad;

        return $this;
    }

    public function getOrigenParada(): ?Parada
    {
        return $this->origen_parada;
    }

    public function setOrigenParada(?Parada $origen_parada): static
    {
        $this->origen_parada = $origen_parada;

        return $this;
    }

    public function getDestinoProvincia(): ?Provincia
    {
        return $this->destino_provincia;
    }

    public function setDestinoProvincia(?Provincia $destino_provincia): static
    {
        $this->destino_provincia = $destino_provincia;

        return $this;
    }

    public function getDestinoCiudad(): ?Ciudad
    {
        return $this->destino_ciudad;
    }

    public function setDestinoCiudad(?Ciudad $destino_ciudad): static
    {
        $this->destino_ciudad = $destino_ciudad;

        return $this;
    }

    public function getDestinoParada(): ?Parada
    {
        return $this->destino_parada;
    }

    public function setDestinoParada(?Parada $destino_parada): static
    {
        $this->destino_parada = $destino_parada;

        return $this;
    }

    public function getCategoriaId(): ?int
    {
        return $this->categoria_id;
    }

    public function setCategoriaId(?int $categoria_id): static
    {
        $this->categoria_id = $categoria_id;

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
}
