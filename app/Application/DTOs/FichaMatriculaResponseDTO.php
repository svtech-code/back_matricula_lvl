<?php

namespace App\Application\DTOs;

use App\Domain\Entities\FichaMatricula;

class FichaMatriculaResponseDTO
{
    public int $id;
    public int $periodo_lectivo;
    public int $grado_a_matricularse;
    public bool $matricula_nueva;
    public int $cod_estado_ficha_matricula;
    public ?string $fecha_prematricula;
    public ?string $fecha_matricula;
    public array $estudiante;
    public array $antecedentes_personales;
    public array $antecedentes_academicos;
    public array $antecedentes_localidad;
    public array $antecedentes_pie;
    public array $antecedentes_salud;
    public array $antecedentes_sociales;
    public array $antecedentes_junaeb;
    public array $familiares;
    public array $formacion_general_opciones;

    public static function fromEntity(FichaMatricula $ficha): self
    {
        $dto = new self();
        $dto->id = $ficha->getId();
        $dto->periodo_lectivo = $ficha->getPeriodoLectivo();
        $dto->grado_a_matricularse = $ficha->getGradoAMatricularse();
        $dto->matricula_nueva = $ficha->getMatriculaNueva();
        $dto->cod_estado_ficha_matricula = $ficha->getCodEstadoFichaMatricula();
        $dto->fecha_prematricula = $ficha->getFechaPrematricula()?->format('Y-m-d H:i:s');
        $dto->fecha_matricula = $ficha->getFechaMatricula()?->format('Y-m-d H:i:s');
        $dto->estudiante = $ficha->getEstudiante()->toArray();
        $dto->antecedentes_personales = $ficha->getAntecedentesPersonales()->toArray();
        $dto->antecedentes_academicos = $ficha->getAntecedentesAcademicos()->toArray();
        $dto->antecedentes_localidad = $ficha->getAntecedentesLocalidad()->toArray();
        $dto->antecedentes_pie = $ficha->getAntecedentesPie()->toArray();
        $dto->antecedentes_salud = $ficha->getAntecedentesSalud()->toArray();
        $dto->antecedentes_sociales = $ficha->getAntecedentesSociales()->toArray();
        $dto->antecedentes_junaeb = $ficha->getAntecedentesJunaeb()->toArray();

        $dto->familiares = array_map(function ($familiar) {
            return $familiar->toArray();
        }, $ficha->getFamiliares());

        $dto->formacion_general_opciones = $ficha->getFormacionGeneralOpciones();

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'periodo_lectivo' => $this->periodo_lectivo,
            'grado_a_matricularse' => $this->grado_a_matricularse,
            'matricula_nueva' => $this->matricula_nueva,
            'cod_estado_ficha_matricula' => $this->cod_estado_ficha_matricula,
            'fecha_prematricula' => $this->fecha_prematricula,
            'fecha_matricula' => $this->fecha_matricula,
            'estudiante' => $this->estudiante,
            'antecedentes_personales' => $this->antecedentes_personales,
            'antecedentes_academicos' => $this->antecedentes_academicos,
            'antecedentes_localidad' => $this->antecedentes_localidad,
            'antecedentes_pie' => $this->antecedentes_pie,
            'antecedentes_salud' => $this->antecedentes_salud,
            'antecedentes_sociales' => $this->antecedentes_sociales,
            'antecedentes_junaeb' => $this->antecedentes_junaeb,
            'familiares' => $this->familiares,
            'formacion_general_opciones' => $this->formacion_general_opciones
        ];
    }
}
