<?php

namespace App\Domain\Entities;

class AntecedentesPersonales
{
    private ?int $codAntecedentesPersonales;
    private ?string $numeroTelefonico;
    private string $numeroTelefonicoEmergencia;
    private ?string $email;
    private ?string $personaConvive;
    private ?string $talentosAcademicos;
    private ?string $diciplinaPracticada;
    private bool $pertenecePrograma_sename;

    public function __construct(
        ?int $codAntecedentesPersonales,
        ?string $numeroTelefonico,
        string $numeroTelefonicoEmergencia,
        ?string $email,
        ?string $personaConvive,
        ?string $talentosAcademicos,
        ?string $diciplinaPracticada,
        bool $pertenecePrograma_sename = false
    ) {
        $this->codAntecedentesPersonales = $codAntecedentesPersonales;
        $this->numeroTelefonico = $numeroTelefonico;
        $this->numeroTelefonicoEmergencia = $numeroTelefonicoEmergencia;
        $this->email = $email;
        $this->personaConvive = $personaConvive;
        $this->talentosAcademicos = $talentosAcademicos;
        $this->diciplinaPracticada = $diciplinaPracticada;
        $this->pertenecePrograma_sename = $pertenecePrograma_sename;
    }

    public function getCodAntecedentesPersonales(): ?int
    {
        return $this->codAntecedentesPersonales;
    }

    public function getNumeroTelefonico(): ?string
    {
        return $this->numeroTelefonico;
    }

    public function getNumeroTelefonicoEmergencia(): string
    {
        return $this->numeroTelefonicoEmergencia;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPersonaConvive(): ?string
    {
        return $this->personaConvive;
    }

    public function getTalentosAcademicos(): ?string
    {
        return $this->talentosAcademicos;
    }

    public function getDiciplinaPracticada(): ?string
    {
        return $this->diciplinaPracticada;
    }

    public function getPertenecePrograma_sename(): bool
    {
        return $this->pertenecePrograma_sename;
    }

    public function toArray(): array
    {
        return [
            'cod_antecedentes_personales' => $this->codAntecedentesPersonales,
            'numero_telefonico' => $this->numeroTelefonico,
            'numero_telefonico_emergencia' => $this->numeroTelefonicoEmergencia,
            'email' => $this->email,
            'persona_convive' => $this->personaConvive,
            'talentos_academicos' => $this->talentosAcademicos,
            'diciplina_practicada' => $this->diciplinaPracticada,
            'pertenece_programa_sename' => $this->pertenecePrograma_sename
        ];
    }
}
