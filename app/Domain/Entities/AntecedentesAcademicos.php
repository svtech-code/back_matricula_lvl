<?php

namespace App\Domain\Entities;

class AntecedentesAcademicos
{
    private ?int $codAntecedentesAcademicos;
    private string $colegioProcedencia;
    private string $cursosReprobados;
    private string $cursoPeriodoAnterior;

    public function __construct(
        ?int $codAntecedentesAcademicos,
        string $colegioProcedencia,
        string $cursosReprobados,
        string $cursoPeriodoAnterior
    ) {
        $this->codAntecedentesAcademicos = $codAntecedentesAcademicos;
        $this->colegioProcedencia = $colegioProcedencia;
        $this->cursosReprobados = $cursosReprobados;
        $this->cursoPeriodoAnterior = $cursoPeriodoAnterior;
    }

    public function getCodAntecedentesAcademicos(): ?int
    {
        return $this->codAntecedentesAcademicos;
    }

    public function getColegioProcedencia(): string
    {
        return $this->colegioProcedencia;
    }

    public function getCursosReprobados(): string
    {
        return $this->cursosReprobados;
    }

    public function getCursoPeriodoAnterior(): string
    {
        return $this->cursoPeriodoAnterior;
    }

    public function toArray(): array
    {
        return [
            'cod_antecedentes_academicos' => $this->codAntecedentesAcademicos,
            'colegio_procedencia' => $this->colegioProcedencia,
            'cursos_reprobados' => $this->cursosReprobados,
            'curso_periodo_anterior' => $this->cursoPeriodoAnterior
        ];
    }
}
