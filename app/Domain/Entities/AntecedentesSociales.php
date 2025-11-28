<?php

namespace App\Domain\Entities;

class AntecedentesSociales
{
    private ?int $codAntecedentesSociales;
    private int $numeroPersonasCasa;
    private int $numeroDormitorios;
    private bool $tieneAguaPotable;
    private bool $tieneLuzElectrica;
    private int $porcentajeSocialHogares;
    private bool $tieneAlcantarillado;
    private string $previsionSalud;
    private bool $subsidioFamiliar;
    private bool $seguroComplementarioSalud;
    private ?string $institucionAtencionSeguro;
    private string $consultorioAtencionPrimaria;

    public function __construct(
        ?int $codAntecedentesSociales,
        int $numeroPersonasCasa,
        int $numeroDormitorios,
        bool $tieneAguaPotable,
        bool $tieneLuzElectrica,
        int $porcentajeSocialHogares,
        bool $tieneAlcantarillado,
        string $previsionSalud,
        bool $subsidioFamiliar,
        bool $seguroComplementarioSalud,
        ?string $institucionAtencionSeguro,
        string $consultorioAtencionPrimaria
    ) {
        $this->codAntecedentesSociales = $codAntecedentesSociales;
        $this->numeroPersonasCasa = $numeroPersonasCasa;
        $this->numeroDormitorios = $numeroDormitorios;
        $this->tieneAguaPotable = $tieneAguaPotable;
        $this->tieneLuzElectrica = $tieneLuzElectrica;
        $this->porcentajeSocialHogares = $porcentajeSocialHogares;
        $this->tieneAlcantarillado = $tieneAlcantarillado;
        $this->previsionSalud = $previsionSalud;
        $this->subsidioFamiliar = $subsidioFamiliar;
        $this->seguroComplementarioSalud = $seguroComplementarioSalud;
        $this->institucionAtencionSeguro = $institucionAtencionSeguro;
        $this->consultorioAtencionPrimaria = $consultorioAtencionPrimaria;
    }

    public function getCodAntecedentesSociales(): ?int
    {
        return $this->codAntecedentesSociales;
    }

    public function getNumeroPersonasCasa(): int
    {
        return $this->numeroPersonasCasa;
    }

    public function getNumeroDormitorios(): int
    {
        return $this->numeroDormitorios;
    }

    public function getTieneAguaPotable(): bool
    {
        return $this->tieneAguaPotable;
    }

    public function getTieneLuzElectrica(): bool
    {
        return $this->tieneLuzElectrica;
    }

    public function getPorcentajeSocialHogares(): int
    {
        return $this->porcentajeSocialHogares;
    }

    public function getTieneAlcantarillado(): bool
    {
        return $this->tieneAlcantarillado;
    }

    public function getPrevisionSalud(): string
    {
        return $this->previsionSalud;
    }

    public function getSubsidioFamiliar(): bool
    {
        return $this->subsidioFamiliar;
    }

    public function getSeguroComplementarioSalud(): bool
    {
        return $this->seguroComplementarioSalud;
    }

    public function getInstitucionAtencionSeguro(): ?string
    {
        return $this->institucionAtencionSeguro;
    }

    public function getConsultorioAtencionPrimaria(): string
    {
        return $this->consultorioAtencionPrimaria;
    }

    public function toArray(): array
    {
        return [
            'cod_antecedentes_sociales' => $this->codAntecedentesSociales,
            'numero_personas_casa' => $this->numeroPersonasCasa,
            'numero_dormitorios' => $this->numeroDormitorios,
            'tiene_agua_potable' => $this->tieneAguaPotable,
            'tiene_luz_electrica' => $this->tieneLuzElectrica,
            'porcentaje_social_hogares' => $this->porcentajeSocialHogares,
            'tiene_alcantarillado' => $this->tieneAlcantarillado,
            'prevision_salud' => $this->previsionSalud,
            'subsidio_familiar' => $this->subsidioFamiliar,
            'seguro_complementario_salud' => $this->seguroComplementarioSalud,
            'institucion_atencion_seguro' => $this->institucionAtencionSeguro,
            'consultorio_atencion_primaria' => $this->consultorioAtencionPrimaria
        ];
    }
}
