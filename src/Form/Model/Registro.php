<?php

namespace App\Form\Model;


class Registro
{
    protected ?string $email;
    protected ?string $nombre;
    protected ?string $apellido;
    protected ?string $nro_documento;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(?string $apellido): void
    {
        $this->apellido = $apellido;
    }

    public function getNroDocumento(): ?int
    {
        return $this->nro_documento;
    }

    public function setNroDocumento(?int $nro_documento): void
    {
        $this->nro_documento = $nro_documento;
    }


}
