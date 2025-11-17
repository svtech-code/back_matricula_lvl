<?php

use Dotenv\Dotenv;
use App\Infrastructure\Persistence\Database;
use App\Infrastructure\Security\JwtService;
use App\Infrastructure\Persistence\UserRepository;
use App\Application\UseCases\LoginUseCase;
use App\Presentation\Controllers\AuthController;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'JWT_SECRET', 'JWT_ALGORITHM', 'JWT_EXPIRATION']);

$container = [];

$container['database'] = function () {
    return new Database(
        $_ENV['DB_HOST'],
        $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'] ?? ''
    );
};

$container['jwtService'] = function () {
    return new JwtService(
        $_ENV['JWT_SECRET'],
        $_ENV['JWT_ALGORITHM'],
        (int)$_ENV['JWT_EXPIRATION']
    );
};

$container['userRepository'] = function () use ($container) {
    return new UserRepository($container['database']());
};

$container['loginUseCase'] = function () use ($container) {
    return new LoginUseCase(
        $container['userRepository'](),
        $container['jwtService']()
    );
};

$container['authController'] = function () use ($container) {
    return new AuthController($container['loginUseCase']());
};

Flight::map('authController', $container['authController']);

return $container;
