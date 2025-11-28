<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\GeneroRepositoryInterface;
use App\Application\DTOs\GeneroResponseDTO;

class GetGeneroUseCase
{
    private GeneroRepositoryInterface $generoRepository;

    public function __construct(GeneroRepositoryInterface $generoRepository)
    {
        $this->generoRepository = $generoRepository;
    }

    public function execute(): GeneroResponseDTO
    {
        $generos = $this->generoRepository->findAll();

        $data = array_map(function($genero) {
            return $genero->toArray();
        }, $generos);

        return new GeneroResponseDTO(true, 'Géneros obtenidos exitosamente', ['generos' => $data]);
    }
}
