<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\MatriculaRepositoryInterface;
use App\Domain\Entities\Matricula;
use App\Application\DTOs\MatriculaResponseDTO;
use DateTime;
use Exception;

class CreateMatriculaUseCase
{
    private MatriculaRepositoryInterface $matriculaRepository;

    public function __construct(MatriculaRepositoryInterface $matriculaRepository)
    {
        $this->matriculaRepository = $matriculaRepository;
    }

    public function execute(
        string $nombres,
        ?string $nombreSocial,
        string $apellidoPaterno,
        string $apellidoMaterno,
        string $fechaNacimiento,
        string $grado
    ): MatriculaResponseDTO {
        try {
            $fechaNacimientoObj = new DateTime($fechaNacimiento);
        } catch (Exception $e) {
            return new MatriculaResponseDTO(false, 'Formato de fecha inválido');
        }

        $matricula = new Matricula(
            null,
            $nombres,
            $nombreSocial,
            $apellidoPaterno,
            $apellidoMaterno,
            $fechaNacimientoObj,
            $grado
        );

        $saved = $this->matriculaRepository->save($matricula);

        if (!$saved) {
            return new MatriculaResponseDTO(false, 'Error al crear matrícula');
        }

        return new MatriculaResponseDTO(true, 'Matrícula creada exitosamente');
    }
}
