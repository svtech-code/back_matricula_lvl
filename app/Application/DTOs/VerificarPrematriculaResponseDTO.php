<?php

namespace App\Application\DTOs;

class VerificarPrematriculaResponseDTO
{
    private int $estadoFichaMatricula;
    private string $fechaPrematricula;

    public function __construct(int $estadoFichaMatricula, string $fechaPrematricula)
    {
        $this->estadoFichaMatricula = $estadoFichaMatricula;
        $this->fechaPrematricula = $fechaPrematricula;
    }

    public function toArray(): array
    {
        return [
            'estado_ficha_matricula' => $this->estadoFichaMatricula,
            'fecha_prematricula' => $this->fechaPrematricula
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['cod_estado_ficha_matricula'],
            $data['fecha_prematricula']
        );
    }
}
