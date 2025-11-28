<?php

Flight::route('GET /api/tipos-familiares', function () {
    $controller = Flight::tipoFamiliarController();
    $controller->getAll();
});
