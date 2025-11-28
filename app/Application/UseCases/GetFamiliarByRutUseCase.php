<?php

namespace App\Application\UseCases;

use App\Application\DTOs\FamiliarResponseDTO;
use App\Domain\Repositories\FamiliarRepositoryInterface;

class GetFamiliarByRutUseCase
{
    private FamiliarRepositoryInterface $familiarRepository;

    public function __construct(FamiliarRepositoryInterface $familiarRepository)
    {
        $this->familiarRepository = $familiarRepository;
    }

    public function execute(int $runFamiliar): FamiliarResponseDTO
    {
        $familiar = $this->familiarRepository->findByRut($runFamiliar);

        if (!$familiar) {
            return new FamiliarResponseDTO(
                false,
                'Familiar no encontrado',
                null
            );
        }

        return new FamiliarResponseDTO(
            true,
            'Familiar encontrado exitosamente',
            $familiar->toArray()
        );
    }
}
