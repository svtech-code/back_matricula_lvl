<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\PasswordService;
use App\Infrastructure\Security\JwtService;
use App\Application\DTOs\LoginResponseDTO;

class LoginUseCase
{
    private UserRepositoryInterface $userRepository;
    private JwtService $jwtService;
    private PasswordService $passwordService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        JwtService $jwtService,
        PasswordService $passwordService
    ) {
        $this->userRepository = $userRepository;
        $this->jwtService = $jwtService;
        $this->passwordService = $passwordService;
    }

    public function execute(string $email, string $password): LoginResponseDTO
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return new LoginResponseDTO(false, 'Credenciales inválidas');
        }

        if (!$this->passwordService->verify($password, $user->getPassword())) {
            return new LoginResponseDTO(false, 'Credenciales inválidas');
        }

        $token = $this->jwtService->generateToken([
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'rol' => $user->getRol()
        ]);

        return new LoginResponseDTO(true, 'Login exitoso', [
            'token' => $token,
            'user' => $user->toArray()
        ]);
    }
}
