<?php

namespace App\Domain\Entities;

class Genero
{
    private ?int $codGenero;
    private string $descripcion;

    public function __construct(?int $codGenero, string $descripcion)
    {
        $this->codGenero = $codGenero;
        $this->descripcion = $descripcion;
    }

    public function getCodGenero(): ?int
    {
        return $this->codGenero;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function toArray(): array
    {
        return [
            'cod_genero' => $this->codGenero,
            'descripcion' => $this->descripcion
        ];
    }
}
