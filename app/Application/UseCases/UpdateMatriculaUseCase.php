<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\MatriculaRepositoryInterface;
use App\Domain\Entities\Matricula;
use App\Application\DTOs\MatriculaResponseDTO;
use DateTime;
use Exception;

class UpdateMatriculaUseCase
{
    private MatriculaRepositoryInterface $matriculaRepository;

    public function __construct(MatriculaRepositoryInterface $matriculaRepository)
    {
        $this->matriculaRepository = $matriculaRepository;
    }

    public function execute(
        int $id,
        string $nombres,
        ?string $nombreSocial,
        string $apellidoPaterno,
        string $apellidoMaterno,
        string $fechaNacimiento,
        string $grado
    ): MatriculaResponseDTO {
        $existingMatricula = $this->matriculaRepository->findById($id);

        if (!$existingMatricula) {
            return new MatriculaResponseDTO(false, 'Matrícula no encontrada');
        }

        try {
            $fechaNacimientoObj = new DateTime($fechaNacimiento);
        } catch (Exception $e) {
            return new MatriculaResponseDTO(false, 'Formato de fecha inválido');
        }

        $matricula = new Matricula(
            $id,
            $nombres,
            $nombreSocial,
            $apellidoPaterno,
            $apellidoMaterno,
            $fechaNacimientoObj,
            $grado
        );

        $updated = $this->matriculaRepository->update($matricula);

        if (!$updated) {
            return new MatriculaResponseDTO(false, 'Error al actualizar matrícula');
        }

        return new MatriculaResponseDTO(true, 'Matrícula actualizada exitosamente', $matricula->toArray());
    }
}
