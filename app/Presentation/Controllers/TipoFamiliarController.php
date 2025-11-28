<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\GetTipoFamiliarUseCase;
use App\Presentation\Http\ResponseInterface;

class TipoFamiliarController
{
    private GetTipoFamiliarUseCase $getTipoFamiliarUseCase;
    private ResponseInterface $response;

    public function __construct(GetTipoFamiliarUseCase $getTipoFamiliarUseCase, ResponseInterface $response)
    {
        $this->getTipoFamiliarUseCase = $getTipoFamiliarUseCase;
        $this->response = $response;
    }

    public function getAll(): void
    {
        $result = $this->getTipoFamiliarUseCase->execute();
        $this->response->json($result->toArray(), 200);
    }
}
