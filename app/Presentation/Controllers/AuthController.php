<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\LoginUseCase;
use App\Presentation\Http\RequestInterface;
use App\Presentation\Http\ResponseInterface;

class AuthController
{
    private LoginUseCase $loginUseCase;
    private RequestInterface $request;
    private ResponseInterface $response;

    public function __construct(
        LoginUseCase $loginUseCase,
        RequestInterface $request,
        ResponseInterface $response
    ) {
        $this->loginUseCase = $loginUseCase;
        $this->request = $request;
        $this->response = $response;
    }

    public function login(): void
    {
        $data = $this->request->getData();

        if (!isset($data->email) || !isset($data->password)) {
            $this->response->json([
                'success' => false,
                'message' => 'Email y password son requeridos'
            ], 400);
            return;
        }

        $email = trim($data->email);
        $password = trim($data->password);

        if (empty($email) || empty($password)) {
            $this->response->json([
                'success' => false,
                'message' => 'Email y password no pueden estar vacíos'
            ], 400);
            return;
        }

        $result = $this->loginUseCase->execute($email, $password);

        $statusCode = $result->toArray()['success'] ? 200 : 401;
        $this->response->json($result->toArray(), $statusCode);
    }
}
