<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\FichaMatriculaRepositoryInterface;

class GetEstudiantesByPeriodoLectivoUseCase
{
    private FichaMatriculaRepositoryInterface $fichaMatriculaRepository;

    public function __construct(FichaMatriculaRepositoryInterface $fichaMatriculaRepository)
    {
        $this->fichaMatriculaRepository = $fichaMatriculaRepository;
    }

    /**
     * Ejecuta el caso de uso para obtener RUN de estudiantes por período lectivo
     *
     * @param int $codPeriodoLectivo Código del período lectivo
     * @return array Array de RUN de estudiantes (solo números, sin dígito verificador)
     */
    public function execute(int $codPeriodoLectivo): array
    {
        return $this->fichaMatriculaRepository->findEstudiantesByPeriodoLectivo($codPeriodoLectivo);
    }
}