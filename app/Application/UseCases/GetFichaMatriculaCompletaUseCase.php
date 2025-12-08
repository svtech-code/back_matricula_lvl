<?php

namespace App\Application\UseCases;

use App\Application\DTOs\FichaMatriculaCompletaResponseDTO;
use App\Domain\Repositories\FichaMatriculaRepositoryInterface;

class GetFichaMatriculaCompletaUseCase
{
    private FichaMatriculaRepositoryInterface $fichaMatriculaRepository;

    public function __construct(FichaMatriculaRepositoryInterface $fichaMatriculaRepository)
    {
        $this->fichaMatriculaRepository = $fichaMatriculaRepository;
    }

    public function execute(int $codFichaMatricula): ?FichaMatriculaCompletaResponseDTO
    {
        $fichaMatricula = $this->fichaMatriculaRepository->findByIdWithAllDetails($codFichaMatricula);
        
        if (!$fichaMatricula) {
            return null;
        }

        return new FichaMatriculaCompletaResponseDTO($fichaMatricula);
    }



    public function executeByEstudianteRunAndCode(int $runEstudiante, int $codPeriodoLectivo): ?FichaMatriculaCompletaResponseDTO
    {
        $fichaMatricula = $this->fichaMatriculaRepository->findByEstudianteAndPeriodoCodigo($runEstudiante, $codPeriodoLectivo);
        
        if (!$fichaMatricula) {
            return null;
        }

        return new FichaMatriculaCompletaResponseDTO($fichaMatricula);
    }
}