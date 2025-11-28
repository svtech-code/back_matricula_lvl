<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\MatriculaRepositoryInterface;
use App\Application\DTOs\MatriculaResponseDTO;

class GetMatriculaUseCase
{
    private MatriculaRepositoryInterface $matriculaRepository;

    public function __construct(MatriculaRepositoryInterface $matriculaRepository)
    {
        $this->matriculaRepository = $matriculaRepository;
    }

    public function execute(int $id): MatriculaResponseDTO
    {
        $matricula = $this->matriculaRepository->findById($id);

        if (!$matricula) {
            return new MatriculaResponseDTO(false, 'Matrícula no encontrada');
        }

        return new MatriculaResponseDTO(true, 'Matrícula obtenida exitosamente', $matricula->toArray());
    }

    public function executeAll(): MatriculaResponseDTO
    {
        $matriculas = $this->matriculaRepository->findAll();

        $data = array_map(function($matricula) {
            return $matricula->toArray();
        }, $matriculas);

        return new MatriculaResponseDTO(true, 'Matrículas obtenidas exitosamente', ['matriculas' => $data]);
    }
}
