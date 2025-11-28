<?php

Flight::route('GET /api/escolaridades', function () {
    $controller = Flight::escolaridadController();
    $controller->getAll();
});
