<?php

namespace App\Domain\Entities;

class FormacionGeneralOpcion
{
    private ?int $codFgOpciones;
    private string $nombreAsignatura;
    private string $categoria;

    public function __construct(
        ?int $codFgOpciones,
        string $nombreAsignatura,
        string $categoria
    ) {
        $this->codFgOpciones = $codFgOpciones;
        $this->nombreAsignatura = $nombreAsignatura;
        $this->categoria = $categoria;
    }

    public function getCodFgOpciones(): ?int
    {
        return $this->codFgOpciones;
    }

    public function getNombreAsignatura(): string
    {
        return $this->nombreAsignatura;
    }

    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public function toArray(): array
    {
        return [
            'cod_fg_opciones' => $this->codFgOpciones,
            'nombre_asignatura' => $this->nombreAsignatura,
            'categoria' => $this->categoria
        ];
    }
}
