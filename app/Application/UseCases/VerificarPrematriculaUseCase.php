<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\FichaMatriculaRepositoryInterface;

class VerificarPrematriculaUseCase
{
    private FichaMatriculaRepositoryInterface $fichaMatriculaRepository;

    public function __construct(FichaMatriculaRepositoryInterface $fichaMatriculaRepository)
    {
        $this->fichaMatriculaRepository = $fichaMatriculaRepository;
    }

    public function execute(int $runEstudiante, int $periodoLectivo, int $estadoFichaMatricula): ?array
    {
        return $this->fichaMatriculaRepository->verificarPrematricula($runEstudiante, $periodoLectivo, $estadoFichaMatricula);
    }
}
