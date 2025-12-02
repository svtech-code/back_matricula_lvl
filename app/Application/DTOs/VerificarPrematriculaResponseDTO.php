<?php

namespace App\Application\DTOs;

class VerificarPrematriculaResponseDTO
{
    private int $estadoFichaMatricula;
    private string $fechaPrematricula;
    private int $gradoMatricula;
    private string $nombres;
    private string $apellidoPaterno;
    private ?string $apellidoMaterno;

    public function __construct(
        int $estadoFichaMatricula,
        string $fechaPrematricula,
        int $gradoMatricula,
        string $nombres,
        string $apellidoPaterno,
        ?string $apellidoMaterno
    ) {
        $this->estadoFichaMatricula = $estadoFichaMatricula;
        $this->fechaPrematricula = $fechaPrematricula;
        $this->gradoMatricula = $gradoMatricula;
        $this->nombres = $nombres;
        $this->apellidoPaterno = $apellidoPaterno;
        $this->apellidoMaterno = $apellidoMaterno;
    }

    public function toArray(): array
    {
        return [
            'estado_ficha_matricula' => $this->estadoFichaMatricula,
            'fecha_prematricula' => $this->fechaPrematricula,
            'grado_a_matricularse' => $this->gradoMatricula,
            'nombres' => $this->nombres,
            'apellido_paterno' => $this->apellidoPaterno,
            'apellido_materno' => $this->apellidoMaterno
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['cod_estado_ficha_matricula'],
            $data['fecha_prematricula'],
            $data['grado_a_matricularse'],
            $data['nombres'],
            $data['apellido_paterno'],
            $data['apellido_materno']
        );
    }
}
