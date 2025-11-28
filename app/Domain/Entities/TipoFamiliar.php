<?php

namespace App\Domain\Entities;

class TipoFamiliar
{
    private ?int $codTipoFamiliar;
    private string $descripcionFamiliar;

    public function __construct(?int $codTipoFamiliar, string $descripcionFamiliar)
    {
        $this->codTipoFamiliar = $codTipoFamiliar;
        $this->descripcionFamiliar = $descripcionFamiliar;
    }

    public function getCodTipoFamiliar(): ?int
    {
        return $this->codTipoFamiliar;
    }

    public function getDescripcionFamiliar(): string
    {
        return $this->descripcionFamiliar;
    }

    public function toArray(): array
    {
        return [
            'cod_tipo_familiar' => $this->codTipoFamiliar,
            'descripcion_familiar' => $this->descripcionFamiliar
        ];
    }
}
