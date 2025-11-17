<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\LoginUseCase;
use Flight;

class AuthController
{
    private LoginUseCase $loginUseCase;

    public function __construct(LoginUseCase $loginUseCase)
    {
        $this->loginUseCase = $loginUseCase;
    }

    public function login(): void
    {
        $data = Flight::request()->data;

        if (!isset($data->email) || !isset($data->password)) {
            Flight::json([
                'success' => false,
                'message' => 'Email y password son requeridos'
            ], 400);
            return;
        }

        $email = trim($data->email);
        $password = trim($data->password);

        if (empty($email) || empty($password)) {
            Flight::json([
                'success' => false,
                'message' => 'Email y password no pueden estar vacíos'
            ], 400);
            return;
        }

        $result = $this->loginUseCase->execute($email, $password);

        $statusCode = $result['success'] ? 200 : 401;
        Flight::json($result, $statusCode);
    }
}
