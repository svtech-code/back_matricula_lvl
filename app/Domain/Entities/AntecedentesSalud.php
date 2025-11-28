<?php

namespace App\Domain\Entities;

class AntecedentesSalud
{
    private ?int $codAntecedentesSalud;
    private ?string $enfermedadDiagnosticada;
    private bool $documentacionEnfermedades;
    private ?string $medicamentosIndicados;
    private ?string $medicamentosContraindicados;
    private ?string $grupoSanguineo;
    private bool $atendidoPsicologo;
    private bool $atendidoPsiquiatra;
    private bool $atendidoPsicopedagogo;
    private bool $atendidoFonoaudiologo;
    private bool $atendidoOtro;
    private ?string $nombreEspecialista;
    private ?string $especialidad;

    public function __construct(
        ?int $codAntecedentesSalud,
        ?string $enfermedadDiagnosticada = null,
        bool $documentacionEnfermedades = false,
        ?string $medicamentosIndicados = null,
        ?string $medicamentosContraindicados = null,
        ?string $grupoSanguineo = null,
        bool $atendidoPsicologo = false,
        bool $atendidoPsiquiatra = false,
        bool $atendidoPsicopedagogo = false,
        bool $atendidoFonoaudiologo = false,
        bool $atendidoOtro = false,
        ?string $nombreEspecialista = null,
        ?string $especialidad = null
    ) {
        $this->codAntecedentesSalud = $codAntecedentesSalud;
        $this->enfermedadDiagnosticada = $enfermedadDiagnosticada;
        $this->documentacionEnfermedades = $documentacionEnfermedades;
        $this->medicamentosIndicados = $medicamentosIndicados;
        $this->medicamentosContraindicados = $medicamentosContraindicados;
        $this->grupoSanguineo = $grupoSanguineo;
        $this->atendidoPsicologo = $atendidoPsicologo;
        $this->atendidoPsiquiatra = $atendidoPsiquiatra;
        $this->atendidoPsicopedagogo = $atendidoPsicopedagogo;
        $this->atendidoFonoaudiologo = $atendidoFonoaudiologo;
        $this->atendidoOtro = $atendidoOtro;
        $this->nombreEspecialista = $nombreEspecialista;
        $this->especialidad = $especialidad;
    }

    public function getCodAntecedentesSalud(): ?int
    {
        return $this->codAntecedentesSalud;
    }

    public function getEnfermedadDiagnosticada(): ?string
    {
        return $this->enfermedadDiagnosticada;
    }

    public function getDocumentacionEnfermedades(): bool
    {
        return $this->documentacionEnfermedades;
    }

    public function getMedicamentosIndicados(): ?string
    {
        return $this->medicamentosIndicados;
    }

    public function getMedicamentosContraindicados(): ?string
    {
        return $this->medicamentosContraindicados;
    }

    public function getGrupoSanguineo(): ?string
    {
        return $this->grupoSanguineo;
    }

    public function getAtendidoPsicologo(): bool
    {
        return $this->atendidoPsicologo;
    }

    public function getAtendidoPsiquiatra(): bool
    {
        return $this->atendidoPsiquiatra;
    }

    public function getAtendidoPsicopedagogo(): bool
    {
        return $this->atendidoPsicopedagogo;
    }

    public function getAtendidoFonoaudiologo(): bool
    {
        return $this->atendidoFonoaudiologo;
    }

    public function getAtendidoOtro(): bool
    {
        return $this->atendidoOtro;
    }

    public function getNombreEspecialista(): ?string
    {
        return $this->nombreEspecialista;
    }

    public function getEspecialidad(): ?string
    {
        return $this->especialidad;
    }

    public function toArray(): array
    {
        return [
            'cod_antecedentes_salud' => $this->codAntecedentesSalud,
            'enfermedad_diagnosticada' => $this->enfermedadDiagnosticada,
            'documentacion_enfermedades' => $this->documentacionEnfermedades,
            'medicamentos_indicados' => $this->medicamentosIndicados,
            'medicamentos_contraindicados' => $this->medicamentosContraindicados,
            'grupo_sanguineo' => $this->grupoSanguineo,
            'atendido_psicologo' => $this->atendidoPsicologo,
            'atendido_psiquiatra' => $this->atendidoPsiquiatra,
            'atendido_psicopedagogo' => $this->atendidoPsicopedagogo,
            'atendido_fonoaudiologo' => $this->atendidoFonoaudiologo,
            'atendido_otro' => $this->atendidoOtro,
            'nombre_especialista' => $this->nombreEspecialista,
            'especialidad' => $this->especialidad
        ];
    }
}
