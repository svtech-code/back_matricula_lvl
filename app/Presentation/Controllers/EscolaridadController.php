<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\GetEscolaridadUseCase;
use App\Presentation\Http\ResponseInterface;

class EscolaridadController
{
    private GetEscolaridadUseCase $getEscolaridadUseCase;
    private ResponseInterface $response;

    public function __construct(GetEscolaridadUseCase $getEscolaridadUseCase, ResponseInterface $response)
    {
        $this->getEscolaridadUseCase = $getEscolaridadUseCase;
        $this->response = $response;
    }

    public function getAll(): void
    {
        $result = $this->getEscolaridadUseCase->execute();
        $this->response->json($result->toArray(), 200);
    }
}
