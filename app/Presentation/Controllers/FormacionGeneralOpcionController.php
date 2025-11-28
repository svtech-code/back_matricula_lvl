<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\GetFormacionGeneralOpcionUseCase;
use App\Presentation\Http\ResponseInterface;

class FormacionGeneralOpcionController
{
    private GetFormacionGeneralOpcionUseCase $getFormacionGeneralOpcionUseCase;
    private ResponseInterface $response;

    public function __construct(GetFormacionGeneralOpcionUseCase $getFormacionGeneralOpcionUseCase, ResponseInterface $response)
    {
        $this->getFormacionGeneralOpcionUseCase = $getFormacionGeneralOpcionUseCase;
        $this->response = $response;
    }

    public function getAll(): void
    {
        $result = $this->getFormacionGeneralOpcionUseCase->execute();
        $this->response->json($result->toArray(), 200);
    }
}
