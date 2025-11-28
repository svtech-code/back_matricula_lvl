<?php

namespace App\Domain\Entities;

use DateTime;

class Matricula
{
    private ?int $id;
    private string $nombres;
    private ?string $nombreSocial;
    private string $apellidoPaterno;
    private string $apellidoMaterno;
    private DateTime $fechaNacimiento;
    private string $grado;

    public function __construct(
        ?int $id,
        string $nombres,
        ?string $nombreSocial,
        string $apellidoPaterno,
        string $apellidoMaterno,
        DateTime $fechaNacimiento,
        string $grado
    ) {
        $this->id = $id;
        $this->nombres = $nombres;
        $this->nombreSocial = $nombreSocial;
        $this->apellidoPaterno = $apellidoPaterno;
        $this->apellidoMaterno = $apellidoMaterno;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->grado = $grado;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getApellidoMaterno(): string
    {
        return $this->apellidoMaterno;
    }

    public function getFechaNacimiento(): DateTime
    {
        return $this->fechaNacimiento;
    }

    public function getGrado(): string
    {
        return $this->grado;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombres' => $this->nombres,
            'nombre_social' => $this->nombreSocial,
            'apellido_paterno' => $this->apellidoPaterno,
            'apellido_materno' => $this->apellidoMaterno,
            'fecha_nacimiento' => $this->fechaNacimiento->format('Y-m-d'),
            'grado' => $this->grado
        ];
    }
}
