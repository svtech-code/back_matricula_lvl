<?php

namespace App\Domain\Entities;

use DateTime;

class Estudiante
{
    private ?int $codEstudiante;
    private int $runEstudiante;
    private string $dvRutEstudiante;
    private string $nombres;
    private ?string $nombreSocial;
    private string $apellidoPaterno;
    private ?string $apellidoMaterno;
    private DateTime $fechaNacimiento;
    private ?string $nacionalidad;
    private int $codGenero;

    public function __construct(
        ?int $codEstudiante,
        int $runEstudiante,
        string $dvRutEstudiante,
        string $nombres,
        ?string $nombreSocial,
        string $apellidoPaterno,
        ?string $apellidoMaterno,
        DateTime $fechaNacimiento,
        ?string $nacionalidad,
        int $codGenero
    ) {
        $this->codEstudiante = $codEstudiante;
        $this->runEstudiante = $runEstudiante;
        $this->dvRutEstudiante = $dvRutEstudiante;
        $this->nombres = $nombres;
        $this->nombreSocial = $nombreSocial;
        $this->apellidoPaterno = $apellidoPaterno;
        $this->apellidoMaterno = $apellidoMaterno;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->nacionalidad = $nacionalidad;
        $this->codGenero = $codGenero;
    }

    public function getCodEstudiante(): ?int
    {
        return $this->codEstudiante;
    }

    public function getRunEstudiante(): int
    {
        return $this->runEstudiante;
    }

    public function getDvRutEstudiante(): string
    {
        return $this->dvRutEstudiante;
    }

    public function getNombres(): string
    {
        return $this->nombres;
    }

    public function getNombreSocial(): ?string
    {
        return $this->nombreSocial;
    }

    public function getApellidoPaterno(): string
    {
        return $this->apellidoPaterno;
    }

    public function getApellidoMaterno(): ?string
    {
        return $this->apellidoMaterno;
    }

    public function getFechaNacimiento(): DateTime
    {
        return $this->fechaNacimiento;
    }

    public function getNacionalidad(): ?string
    {
        return $this->nacionalidad;
    }

    public function getCodGenero(): int
    {
        return $this->codGenero;
    }

    public function toArray(): array
    {
        return [
            'cod_estudiante' => $this->codEstudiante,
            'run_estudiante' => $this->runEstudiante,
            'dv_rut_estudiante' => $this->dvRutEstudiante,
            'nombres' => $this->nombres,
            'nombre_social' => $this->nombreSocial,
            'apellido_paterno' => $this->apellidoPaterno,
            'apellido_materno' => $this->apellidoMaterno,
            'fecha_nacimiento' => $this->fechaNacimiento->format('Y-m-d'),
            'nacionalidad' => $this->nacionalidad,
            'cod_genero' => $this->codGenero
        ];
    }
}
