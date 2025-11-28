<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\FormacionGeneralOpcionRepositoryInterface;
use App\Application\DTOs\FormacionGeneralOpcionResponseDTO;

class GetFormacionGeneralOpcionUseCase
{
    private FormacionGeneralOpcionRepositoryInterface $formacionGeneralOpcionRepository;

    public function __construct(FormacionGeneralOpcionRepositoryInterface $formacionGeneralOpcionRepository)
    {
        $this->formacionGeneralOpcionRepository = $formacionGeneralOpcionRepository;
    }

    public function execute(): FormacionGeneralOpcionResponseDTO
    {
        $opciones = $this->formacionGeneralOpcionRepository->findAll();

        $data = array_map(function($opcion) {
            return $opcion->toArray();
        }, $opciones);

        return new FormacionGeneralOpcionResponseDTO(true, 'Opciones de formación general obtenidas exitosamente', ['opciones' => $data]);
    }
}
