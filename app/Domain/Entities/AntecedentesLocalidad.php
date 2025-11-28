<?php

namespace App\Domain\Entities;

class AntecedentesLocalidad
{
    private ?int $codAntecedentesLocalidad;
    private string $direccion;
    private ?string $referenciaDireccion;
    private string $comuna;
    private bool $viveSectorRural;
    private bool $tieneAccesoInternet;

    public function __construct(
        ?int $codAntecedentesLocalidad,
        string $direccion,
        ?string $referenciaDireccion,
        string $comuna,
        bool $viveSectorRural = false,
        bool $tieneAccesoInternet = false
    ) {
        $this->codAntecedentesLocalidad = $codAntecedentesLocalidad;
        $this->direccion = $direccion;
        $this->referenciaDireccion = $referenciaDireccion;
        $this->comuna = $comuna;
        $this->viveSectorRural = $viveSectorRural;
        $this->tieneAccesoInternet = $tieneAccesoInternet;
    }

    public function getCodAntecedentesLocalidad(): ?int
    {
        return $this->codAntecedentesLocalidad;
    }

    public function getDireccion(): string
    {
        return $this->direccion;
    }

    public function getReferenciaDireccion(): ?string
    {
        return $this->referenciaDireccion;
    }

    public function getComuna(): string
    {
        return $this->comuna;
    }

    public function getViveSectorRural(): bool
    {
        return $this->viveSectorRural;
    }

    public function getTieneAccesoInternet(): bool
    {
        return $this->tieneAccesoInternet;
    }

    public function toArray(): array
    {
        return [
            'cod_antecedentes_localidad' => $this->codAntecedentesLocalidad,
            'direccion' => $this->direccion,
            'referencia_direccion' => $this->referenciaDireccion,
            'comuna' => $this->comuna,
            'vive_sector_rural' => $this->viveSectorRural,
            'tiene_acceso_internet' => $this->tieneAccesoInternet
        ];
    }
}
