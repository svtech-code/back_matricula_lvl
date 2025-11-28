<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\TipoFamiliarRepositoryInterface;
use App\Application\DTOs\TipoFamiliarResponseDTO;

class GetTipoFamiliarUseCase
{
    private TipoFamiliarRepositoryInterface $tipoFamiliarRepository;

    public function __construct(TipoFamiliarRepositoryInterface $tipoFamiliarRepository)
    {
        $this->tipoFamiliarRepository = $tipoFamiliarRepository;
    }

    public function execute(): TipoFamiliarResponseDTO
    {
        $tiposFamiliares = $this->tipoFamiliarRepository->findAll();

        $data = array_map(function($tipoFamiliar) {
            return $tipoFamiliar->toArray();
        }, $tiposFamiliares);

        return new TipoFamiliarResponseDTO(true, 'Tipos de familiares obtenidos exitosamente', ['tipos_familiares' => $data]);
    }
}
