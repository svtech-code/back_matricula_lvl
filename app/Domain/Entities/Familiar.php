<?php

namespace App\Domain\Entities;

class Familiar
{
    private ?int $codFamiliar;
    private int $runFamiliar;
    private string $dvRunFamiliar;
    private string $nombres;
    private string $apellidoPaterno;
    private ?string $apellidoMaterno;
    private string $direccion;
    private string $comuna;
    private ?string $actividadLaboral;
    private int $codEscolaridad;
    private string $lugarTrabajo;
    private string $email;
    private string $numeroTelefonico;
    private int $codTipoFamiliar;
    private bool $esTitular;
    private bool $esSuplente;

    public function __construct(
        ?int $codFamiliar,
        int $runFamiliar,
        string $dvRunFamiliar,
        string $nombres,
        string $apellidoPaterno,
        ?string $apellidoMaterno,
        string $direccion,
        string $comuna,
        ?string $actividadLaboral,
        int $codEscolaridad,
        string $lugarTrabajo,
        string $email,
        string $numeroTelefonico,
        int $codTipoFamiliar,
        bool $esTitular = false,
        bool $esSuplente = false
    ) {
        $this->codFamiliar = $codFamiliar;
        $this->runFamiliar = $runFamiliar;
        $this->dvRunFamiliar = $dvRunFamiliar;
        $this->nombres = $nombres;
        $this->apellidoPaterno = $apellidoPaterno;
        $this->apellidoMaterno = $apellidoMaterno;
        $this->direccion = $direccion;
        $this->comuna = $comuna;
        $this->actividadLaboral = $actividadLaboral;
        $this->codEscolaridad = $codEscolaridad;
        $this->lugarTrabajo = $lugarTrabajo;
        $this->email = $email;
        $this->numeroTelefonico = $numeroTelefonico;
        $this->codTipoFamiliar = $codTipoFamiliar;
        $this->esTitular = $esTitular;
        $this->esSuplente = $esSuplente;
    }

    public function getCodFamiliar(): ?int
    {
        return $this->codFamiliar;
    }

    public function setCodFamiliar(int $codFamiliar): void
    {
        $this->codFamiliar = $codFamiliar;
    }

    public function getRunFamiliar(): int
    {
        return $this->runFamiliar;
    }

    public function getDvRunFamiliar(): string
    {
        return $this->dvRunFamiliar;
    }

    public function getNombres(): string
    {
        return $this->nombres;
    }

    public function getApellidoPaterno(): string
    {
        return $this->apellidoPaterno;
    }

    public function getApellidoMaterno(): ?string
    {
        return $this->apellidoMaterno;
    }

    public function getDireccion(): string
    {
        return $this->direccion;
    }

    public function getComuna(): string
    {
        return $this->comuna;
    }

    public function getActividadLaboral(): ?string
    {
        return $this->actividadLaboral;
    }

    public function getCodEscolaridad(): int
    {
        return $this->codEscolaridad;
    }

    public function getLugarTrabajo(): string
    {
        return $this->lugarTrabajo;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getNumeroTelefonico(): string
    {
        return $this->numeroTelefonico;
    }

    public function getCodTipoFamiliar(): int
    {
        return $this->codTipoFamiliar;
    }

    public function getEsTitular(): bool
    {
        return $this->esTitular;
    }

    public function getEsSuplente(): bool
    {
        return $this->esSuplente;
    }

    public function toArray(): array
    {
        return [
            'cod_familiar' => $this->codFamiliar,
            'run_familiar' => $this->runFamiliar,
            'dv_run_familiar' => $this->dvRunFamiliar,
            'nombres' => $this->nombres,
            'apellido_paterno' => $this->apellidoPaterno,
            'apellido_materno' => $this->apellidoMaterno,
            'direccion' => $this->direccion,
            'comuna' => $this->comuna,
            'actividad_laboral' => $this->actividadLaboral,
            'cod_escolaridad' => $this->codEscolaridad,
            'lugar_trabajo' => $this->lugarTrabajo,
            'email' => $this->email,
            'numero_telefonico' => $this->numeroTelefonico,
            'cod_tipo_familiar' => $this->codTipoFamiliar,
            'es_titular' => $this->esTitular,
            'es_suplente' => $this->esSuplente
        ];
    }
}
