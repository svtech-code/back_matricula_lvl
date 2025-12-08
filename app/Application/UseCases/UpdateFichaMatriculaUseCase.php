<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\FichaMatriculaRepositoryInterface;
use App\Application\DTOs\FichaMatriculaCompletaResponseDTO;

class UpdateFichaMatriculaUseCase
{
    private FichaMatriculaRepositoryInterface $fichaMatriculaRepository;

    public function __construct(FichaMatriculaRepositoryInterface $fichaMatriculaRepository)
    {
        $this->fichaMatriculaRepository = $fichaMatriculaRepository;
    }

    public function execute(int $codFichaMatricula, array $data): ?FichaMatriculaCompletaResponseDTO
    {
        // Verificar que la ficha matrícula existe
        $fichaExistente = $this->fichaMatriculaRepository->findByIdWithAllDetails($codFichaMatricula);
        if (!$fichaExistente) {
            return null;
        }

        // Actualizar la ficha matrícula con los nuevos datos
        $actualizada = $this->fichaMatriculaRepository->updateFichaMatriculaCompleta($codFichaMatricula, $data);
        
        if (!$actualizada) {
            throw new \RuntimeException("Error al actualizar la ficha de matrícula");
        }

        // Obtener la ficha actualizada
        $fichaActualizada = $this->fichaMatriculaRepository->findByIdWithAllDetails($codFichaMatricula);
        
        return new FichaMatriculaCompletaResponseDTO($fichaActualizada);
    }
}