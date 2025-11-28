<?php

Flight::route('POST /api/ficha-matricula', function () {
    $controller = Flight::fichaMatriculaController();
    $controller->create();
});

Flight::route('GET /api/ficha-matricula/verificar', function () {
    $controller = Flight::fichaMatriculaController();
    $controller->verificar();
});
