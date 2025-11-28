<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\GetGeneroUseCase;
use App\Presentation\Http\ResponseInterface;

class GeneroController
{
    private GetGeneroUseCase $getGeneroUseCase;
    private ResponseInterface $response;

    public function __construct(GetGeneroUseCase $getGeneroUseCase, ResponseInterface $response)
    {
        $this->getGeneroUseCase = $getGeneroUseCase;
        $this->response = $response;
    }

    public function getAll(): void
    {
        $result = $this->getGeneroUseCase->execute();
        $this->response->json($result->toArray(), 200);
    }
}
