<?php

namespace App\Domain\Entities;

class Escolaridad
{
    private ?int $codEscolaridad;
    private string $descripcion;

    public function __construct(?int $codEscolaridad, string $descripcion)
    {
        $this->codEscolaridad = $codEscolaridad;
        $this->descripcion = $descripcion;
    }

    public function getCodEscolaridad(): ?int
    {
        return $this->codEscolaridad;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function toArray(): array
    {
        return [
            'cod_escolaridad' => $this->codEscolaridad,
            'descripcion' => $this->descripcion
        ];
    }
}
