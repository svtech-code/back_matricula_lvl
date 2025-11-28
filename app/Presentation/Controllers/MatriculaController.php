<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\GetMatriculaUseCase;
use App\Application\UseCases\CreateMatriculaUseCase;
use App\Application\UseCases\UpdateMatriculaUseCase;
use App\Presentation\Http\RequestInterface;
use App\Presentation\Http\ResponseInterface;

class MatriculaController
{
    private GetMatriculaUseCase $getMatriculaUseCase;
    private CreateMatriculaUseCase $createMatriculaUseCase;
    private UpdateMatriculaUseCase $updateMatriculaUseCase;
    private RequestInterface $request;
    private ResponseInterface $response;

    public function __construct(
        GetMatriculaUseCase $getMatriculaUseCase,
        CreateMatriculaUseCase $createMatriculaUseCase,
        UpdateMatriculaUseCase $updateMatriculaUseCase,
        RequestInterface $request,
        ResponseInterface $response
    ) {
        $this->getMatriculaUseCase = $getMatriculaUseCase;
        $this->createMatriculaUseCase = $createMatriculaUseCase;
        $this->updateMatriculaUseCase = $updateMatriculaUseCase;
        $this->request = $request;
        $this->response = $response;
    }

    public function getAll(): void
    {
        $result = $this->getMatriculaUseCase->executeAll();
        $this->response->json($result->toArray(), 200);
    }

    public function getById(int $id): void
    {
        $result = $this->getMatriculaUseCase->execute($id);
        $statusCode = $result->toArray()['success'] ? 200 : 404;
        $this->response->json($result->toArray(), $statusCode);
    }

    public function create(): void
    {
        $data = $this->request->getData();

        $requiredFields = ['nombres', 'apellido_paterno', 'apellido_materno', 'fecha_nacimiento', 'grado'];
        foreach ($requiredFields as $field) {
            if (!isset($data->$field)) {
                $this->response->json([
                    'success' => false,
                    'message' => "El campo {$field} es requerido"
                ], 400);
                return;
            }
        }

        $result = $this->createMatriculaUseCase->execute(
            trim($data->nombres),
            isset($data->nombre_social) ? trim($data->nombre_social) : null,
            trim($data->apellido_paterno),
            trim($data->apellido_materno),
            trim($data->fecha_nacimiento),
            trim($data->grado)
        );

        $statusCode = $result->toArray()['success'] ? 201 : 400;
        $this->response->json($result->toArray(), $statusCode);
    }

    public function update(int $id): void
    {
        $data = $this->request->getData();

        $requiredFields = ['nombres', 'apellido_paterno', 'apellido_materno', 'fecha_nacimiento', 'grado'];
        foreach ($requiredFields as $field) {
            if (!isset($data->$field)) {
                $this->response->json([
                    'success' => false,
                    'message' => "El campo {$field} es requerido"
                ], 400);
                return;
            }
        }

        $result = $this->updateMatriculaUseCase->execute(
            $id,
            trim($data->nombres),
            isset($data->nombre_social) ? trim($data->nombre_social) : null,
            trim($data->apellido_paterno),
            trim($data->apellido_materno),
            trim($data->fecha_nacimiento),
            trim($data->grado)
        );

        $statusCode = $result->toArray()['success'] ? 200 : 400;
        $this->response->json($result->toArray(), $statusCode);
    }
}
