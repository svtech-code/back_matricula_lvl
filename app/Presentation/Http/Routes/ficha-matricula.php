<?php

Flight::route('POST /api/ficha-matricula', function () {
    $controller = Flight::fichaMatriculaController();
    $controller->create();
});

Flight::route('GET /api/ficha-matricula/verificar', function () {
    $controller = Flight::fichaMatriculaController();
    $controller->verificar();
});

Flight::route('GET /api/ficha-matricula/completa', function () {
    $controller = Flight::fichaMatriculaController();
    $controller->getFichaCompleta();
});

Flight::route('GET /api/ficha-matricula/estudiantes', function () {
    $controller = Flight::fichaMatriculaController();
    $controller->getEstudiantesByPeriodoLectivo();
});

Flight::route('PATCH /api/ficha-matricula', function () {
    $controller = Flight::fichaMatriculaController();
    $controller->update();
});
