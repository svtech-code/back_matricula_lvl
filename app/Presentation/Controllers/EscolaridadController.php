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
        try {
            $result = $this->getEscolaridadUseCase->execute();
            $responseArray = $result->toArray();
            
            if ($responseArray['success']) {
                $this->response->json($responseArray, 200);
            } else {
                $this->response->json($responseArray, 500);
            }
        } catch (\Exception $e) {
            $this->response->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}
