<?php

use Dotenv\Dotenv;
use App\Infrastructure\Persistence\Database;
use App\Infrastructure\Persistence\DatabaseInterface;
use App\Infrastructure\Security\JwtService;
use App\Infrastructure\Services\EmailService;
use App\Infrastructure\Services\EmailDataMapper;
use App\Infrastructure\Persistence\UserRepository;
use App\Infrastructure\Persistence\MatriculaRepository;
use App\Infrastructure\Persistence\FichaMatriculaRepository;
use App\Infrastructure\Persistence\GeneroRepository;
use App\Infrastructure\Persistence\EscolaridadRepository;
use App\Infrastructure\Persistence\FormacionGeneralOpcionRepository;
use App\Infrastructure\Persistence\TipoFamiliarRepository;
use App\Infrastructure\Persistence\FamiliarRepository;
use App\Domain\Services\PasswordService;
use App\Application\UseCases\LoginUseCase;
use App\Application\UseCases\GetMatriculaUseCase;
use App\Application\UseCases\CreateMatriculaUseCase;
use App\Application\UseCases\UpdateMatriculaUseCase;
use App\Application\UseCases\CreateFichaMatriculaUseCase;
use App\Application\UseCases\UpdateFichaMatriculaUseCase;
use App\Application\UseCases\VerificarPrematriculaUseCase;
use App\Application\UseCases\GetFichaMatriculaCompletaUseCase;
use App\Application\UseCases\GetEstudiantesByPeriodoLectivoUseCase;
use App\Application\UseCases\GetGeneroUseCase;
use App\Application\UseCases\GetEscolaridadUseCase;
use App\Application\UseCases\GetFormacionGeneralOpcionUseCase;
use App\Application\UseCases\GetTipoFamiliarUseCase;
use App\Application\UseCases\GetFamiliarByRutUseCase;
use App\Presentation\Controllers\AuthController;
use App\Presentation\Controllers\MatriculaController;
use App\Presentation\Controllers\FichaMatriculaController;
use App\Presentation\Controllers\GeneroController;
use App\Presentation\Controllers\EscolaridadController;
use App\Presentation\Controllers\FormacionGeneralOpcionController;
use App\Presentation\Controllers\TipoFamiliarController;
use App\Presentation\Controllers\FamiliarController;
use App\Presentation\Http\FlightRequest;
use App\Presentation\Http\FlightResponse;
use App\Presentation\Http\RequestInterface;
use App\Presentation\Http\ResponseInterface;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$dotenv->required([
    'DB_HOST',
    'DB_NAME',
    'DB_USER',
    'JWT_SECRET',
    'JWT_ALGORITHM',
    'JWT_EXPIRATION'
]);

$container = [];

$container['database'] = function () {
    return new Database();
};

$container['jwtService'] = function () {
    return new JwtService(
        $_ENV['JWT_SECRET'],
        $_ENV['JWT_ALGORITHM'],
        (int)$_ENV['JWT_EXPIRATION']
    );
};

$container['passwordService'] = function () {
    return new PasswordService();
};

$container['emailService'] = function () {
    return new EmailService();
};

$container['emailDataMapper'] = function () {
    return new EmailDataMapper();
};

$container['request'] = function () {
    return new FlightRequest();
};

$container['response'] = function () {
    return new FlightResponse();
};

$container['userRepository'] = function () use ($container) {
    return new UserRepository($container['database']());
};

$container['matriculaRepository'] = function () use ($container) {
    return new MatriculaRepository($container['database']());
};

$container['fichaMatriculaRepository'] = function () use ($container) {
    return new FichaMatriculaRepository($container['database']());
};

$container['generoRepository'] = function () use ($container) {
    return new GeneroRepository($container['database']());
};

$container['escolaridadRepository'] = function () use ($container) {
    return new EscolaridadRepository($container['database']());
};

$container['formacionGeneralOpcionRepository'] = function () use ($container) {
    return new FormacionGeneralOpcionRepository($container['database']());
};

$container['tipoFamiliarRepository'] = function () use ($container) {
    return new TipoFamiliarRepository($container['database']());
};

$container['familiarRepository'] = function () use ($container) {
    return new FamiliarRepository($container['database']());
};

$container['loginUseCase'] = function () use ($container) {
    return new LoginUseCase(
        $container['userRepository'](),
        $container['jwtService'](),
        $container['passwordService']()
    );
};

$container['getMatriculaUseCase'] = function () use ($container) {
    return new GetMatriculaUseCase($container['matriculaRepository']());
};

$container['createMatriculaUseCase'] = function () use ($container) {
    return new CreateMatriculaUseCase($container['matriculaRepository']());
};

$container['updateMatriculaUseCase'] = function () use ($container) {
    return new UpdateMatriculaUseCase($container['matriculaRepository']());
};

$container['createFichaMatriculaUseCase'] = function () use ($container) {
    return new CreateFichaMatriculaUseCase($container['fichaMatriculaRepository']());
};

$container['verificarPrematriculaUseCase'] = function () use ($container) {
    return new VerificarPrematriculaUseCase($container['fichaMatriculaRepository']());
};

$container['getFichaMatriculaCompletaUseCase'] = function () use ($container) {
    return new GetFichaMatriculaCompletaUseCase($container['fichaMatriculaRepository']());
};

$container['getEstudiantesByPeriodoLectivoUseCase'] = function () use ($container) {
    return new GetEstudiantesByPeriodoLectivoUseCase($container['fichaMatriculaRepository']());
};

$container['getGeneroUseCase'] = function () use ($container) {
    return new GetGeneroUseCase($container['generoRepository']());
};

$container['getEscolaridadUseCase'] = function () use ($container) {
    return new GetEscolaridadUseCase($container['escolaridadRepository']());
};

$container['getFormacionGeneralOpcionUseCase'] = function () use ($container) {
    return new GetFormacionGeneralOpcionUseCase($container['formacionGeneralOpcionRepository']());
};

$container['getTipoFamiliarUseCase'] = function () use ($container) {
    return new GetTipoFamiliarUseCase($container['tipoFamiliarRepository']());
};

$container['getFamiliarByRutUseCase'] = function () use ($container) {
    return new GetFamiliarByRutUseCase($container['familiarRepository']());
};

$container['updateFichaMatriculaUseCase'] = function () use ($container) {
    return new UpdateFichaMatriculaUseCase($container['fichaMatriculaRepository']());
};

$container['authController'] = function () use ($container) {
    return new AuthController(
        $container['loginUseCase'](),
        $container['request'](),
        $container['response']()
    );
};

$container['matriculaController'] = function () use ($container) {
    return new MatriculaController(
        $container['getMatriculaUseCase'](),
        $container['createMatriculaUseCase'](),
        $container['updateMatriculaUseCase'](),
        $container['request'](),
        $container['response']()
    );
};

$container['fichaMatriculaController'] = function () use ($container) {
    return new FichaMatriculaController(
        $container['createFichaMatriculaUseCase'](),
        $container['verificarPrematriculaUseCase'](),
        $container['getFichaMatriculaCompletaUseCase'](),
        $container['getEstudiantesByPeriodoLectivoUseCase'](),
        $container['updateFichaMatriculaUseCase'](),
        $container['request'](),
        $container['response'](),
        $container['emailService'](),
        $container['emailDataMapper']()
    );
};

$container['generoController'] = function () use ($container) {
    return new GeneroController(
        $container['getGeneroUseCase'](),
        $container['response']()
    );
};

$container['escolaridadController'] = function () use ($container) {
    return new EscolaridadController(
        $container['getEscolaridadUseCase'](),
        $container['response']()
    );
};

$container['formacionGeneralOpcionController'] = function () use ($container) {
    return new FormacionGeneralOpcionController(
        $container['getFormacionGeneralOpcionUseCase'](),
        $container['response']()
    );
};

$container['tipoFamiliarController'] = function () use ($container) {
    return new TipoFamiliarController(
        $container['getTipoFamiliarUseCase'](),
        $container['response']()
    );
};

$container['familiarController'] = function () use ($container) {
    return new FamiliarController(
        $container['getFamiliarByRutUseCase'](),
        $container['request'](),
        $container['response']()
    );
};

Flight::map('authController', $container['authController']);
Flight::map('matriculaController', $container['matriculaController']);
Flight::map('fichaMatriculaController', $container['fichaMatriculaController']);
Flight::map('generoController', $container['generoController']);
Flight::map('escolaridadController', $container['escolaridadController']);
Flight::map('formacionGeneralOpcionController', $container['formacionGeneralOpcionController']);
Flight::map('tipoFamiliarController', $container['tipoFamiliarController']);
Flight::map('familiarController', $container['familiarController']);

return $container;
