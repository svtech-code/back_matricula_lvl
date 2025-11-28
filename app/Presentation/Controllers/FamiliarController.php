<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\GetFamiliarByRutUseCase;
use App\Presentation\Http\RequestInterface;
use App\Presentation\Http\ResponseInterface;

class FamiliarController
{
    private GetFamiliarByRutUseCase $getFamiliarByRutUseCase;
    private RequestInterface $request;
    private ResponseInterface $response;

    public function __construct(
        GetFamiliarByRutUseCase $getFamiliarByRutUseCase,
        RequestInterface $request,
        ResponseInterface $response
    ) {
        $this->getFamiliarByRutUseCase = $getFamiliarByRutUseCase;
        $this->request = $request;
        $this->response = $response;
    }

    public function getByRut(int $rut): void
    {
        $result = $this->getFamiliarByRutUseCase->execute($rut);
        
        $statusCode = $result->toArray()['success'] ? 200 : 404;
        $this->response->json($result->toArray(), $statusCode);
    }
}
