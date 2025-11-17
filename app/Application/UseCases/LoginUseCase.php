<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Infrastructure\Security\JwtService;

class LoginUseCase
{
    private UserRepositoryInterface $userRepository;
    private JwtService $jwtService;

    public function __construct(UserRepositoryInterface $userRepository, JwtService $jwtService)
    {
        $this->userRepository = $userRepository;
        $this->jwtService = $jwtService;
    }

    public function execute(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Credenciales inválidas'
            ];
        }

        if (!$user->verifyPassword($password)) {
            return [
                'success' => false,
                'message' => 'Credenciales inválidas'
            ];
        }

        $token = $this->jwtService->generateToken([
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'rol' => $user->getRol()
        ]);

        return [
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'token' => $token,
                'user' => $user->toArray()
            ]
        ];
    }
}
