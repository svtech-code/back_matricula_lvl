<?php

namespace App\Domain\Entities;

class AntecedentesJunaeb
{
    private ?int $codAntecedentesJunaeb;
    private bool $beneficioAlimentacion;
    private ?string $etniaPerteneciente;
    private bool $becaIndigena;
    private bool $becaPresidenteRepublica;
    private bool $perteneceChileSolidario;

    public function __construct(
        ?int $codAntecedentesJunaeb,
        bool $beneficioAlimentacion = false,
        ?string $etniaPerteneciente = null,
        bool $becaIndigena = false,
        bool $becaPresidenteRepublica = false,
        bool $perteneceChileSolidario = false
    ) {
        $this->codAntecedentesJunaeb = $codAntecedentesJunaeb;
        $this->beneficioAlimentacion = $beneficioAlimentacion;
        $this->etniaPerteneciente = $etniaPerteneciente;
        $this->becaIndigena = $becaIndigena;
        $this->becaPresidenteRepublica = $becaPresidenteRepublica;
        $this->perteneceChileSolidario = $perteneceChileSolidario;
    }

    public function getCodAntecedentesJunaeb(): ?int
    {
        return $this->codAntecedentesJunaeb;
    }

    public function getBeneficioAlimentacion(): bool
    {
        return $this->beneficioAlimentacion;
    }

    public function getEtniaPerteneciente(): ?string
    {
        return $this->etniaPerteneciente;
    }

    public function getBecaIndigena(): bool
    {
        return $this->becaIndigena;
    }

    public function getBecaPresidenteRepublica(): bool
    {
        return $this->becaPresidenteRepublica;
    }

    public function getPerteneceChileSolidario(): bool
    {
        return $this->perteneceChileSolidario;
    }

    public function toArray(): array
    {
        return [
            'cod_antecedentes_junaeb' => $this->codAntecedentesJunaeb,
            'beneficio_alimentacion' => $this->beneficioAlimentacion,
            'etnia_perteneciente' => $this->etniaPerteneciente,
            'beca_indigena' => $this->becaIndigena,
            'beca_presidente_republica' => $this->becaPresidenteRepublica,
            'pertenece_chile_solidario' => $this->perteneceChileSolidario
        ];
    }
}
