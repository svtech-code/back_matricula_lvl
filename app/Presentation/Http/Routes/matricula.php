<?php

Flight::route('GET /api/matriculas', function () {
    $controller = Flight::matriculaController();
    $controller->getAll();
});

Flight::route('GET /api/matriculas/@id:[0-9]+', function (int $id) {
    $controller = Flight::matriculaController();
    $controller->getById($id);
});

Flight::route('POST /api/matriculas', function () {
    $controller = Flight::matriculaController();
    $controller->create();
});

Flight::route('PUT /api/matriculas/@id:[0-9]+', function (int $id) {
    $controller = Flight::matriculaController();
    $controller->update($id);
});
