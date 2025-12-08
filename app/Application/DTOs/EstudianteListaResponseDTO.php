<?php

namespace App\Application\DTOs;

class EstudianteListaResponseDTO
{
    private string $rut;
    private int $runEstudiante;
    private string $dvRutEstudiante;
    private string $nombres;
    private string $apellidoPaterno;
    private ?string $apellidoMaterno;
    private string $nombreCompleto;
    private int $gradoAMatricularse;
    private ?string $fechaPrematricula;
    private int $codEstadoFichaMatricula;

    public function __construct(
        string $rut,
        int $runEstudiante,
        string $dvRutEstudiante,
        string $nombres,
        string $apellidoPaterno,
        ?string $apellidoMaterno,
        string $nombreCompleto,
        int $gradoAMatricularse,
        ?string $fechaPrematricula,
        int $codEstadoFichaMatricula
    ) {
        $this->rut = $rut;
        $this->runEstudiante = $runEstudiante;
        $this->dvRutEstudiante = $dvRutEstudiante;
        $this->nombres = $nombres;
        $this->apellidoPaterno = $apellidoPaterno;
        $this->apellidoMaterno = $apellidoMaterno;
        $this->nombreCompleto = $nombreCompleto;
        $this->gradoAMatricularse = $gradoAMatricularse;
        $this->fechaPrematricula = $fechaPrematricula;
        $this->codEstadoFichaMatricula = $codEstadoFichaMatricula;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['rut'],
            $data['run_estudiante'],
            $data['dv_rut_estudiante'],
            $data['nombres'],
            $data['apellido_paterno'],
            $data['apellido_materno'] ?? null,
            $data['nombre_completo'],
            $data['grado_a_matricularse'],
            $data['fecha_prematricula'] ?? null,
            $data['cod_estado_ficha_matricula']
        );
    }

    public function toArray(): array
    {
        return [
            'rut' => $this->rut,
            'run_estudiante' => $this->runEstudiante,
            'dv_rut_estudiante' => $this->dvRutEstudiante,
            'nombres' => $this->nombres,
            'apellido_paterno' => $this->apellidoPaterno,
            'apellido_materno' => $this->apellidoMaterno,
            'nombre_completo' => $this->nombreCompleto,
            'grado_a_matricularse' => $this->gradoAMatricularse,
            'fecha_prematricula' => $this->fechaPrematricula,
            'cod_estado_ficha_matricula' => $this->codEstadoFichaMatricula
        ];
    }

    // Getters
    public function getRut(): string { return $this->rut; }
    public function getRunEstudiante(): int { return $this->runEstudiante; }
    public function getDvRutEstudiante(): string { return $this->dvRutEstudiante; }
    public function getNombres(): string { return $this->nombres; }
    public function getApellidoPaterno(): string { return $this->apellidoPaterno; }
    public function getApellidoMaterno(): ?string { return $this->apellidoMaterno; }
    public function getNombreCompleto(): string { return $this->nombreCompleto; }
    public function getGradoAMatricularse(): int { return $this->gradoAMatricularse; }
    public function getFechaPrematricula(): ?string { return $this->fechaPrematricula; }
    public function getCodEstadoFichaMatricula(): int { return $this->codEstadoFichaMatricula; }
}