<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\EscolaridadRepositoryInterface;
use App\Application\DTOs\EscolaridadResponseDTO;

class GetEscolaridadUseCase
{
    private EscolaridadRepositoryInterface $escolaridadRepository;

    public function __construct(EscolaridadRepositoryInterface $escolaridadRepository)
    {
        $this->escolaridadRepository = $escolaridadRepository;
    }

    public function execute(): EscolaridadResponseDTO
    {
        try {
            $escolaridades = $this->escolaridadRepository->findAll();

            $data = array_map(function($escolaridad) {
                return $escolaridad->toArray();
            }, $escolaridades);

            return new EscolaridadResponseDTO(true, 'Escolaridades obtenidas exitosamente', ['escolaridades' => $data]);
        } catch (\Exception $e) {
            return new EscolaridadResponseDTO(false, 'Error al obtener escolaridades: ' . $e->getMessage());
        }
    }
}
