<?php

Flight::route('GET /api/generos', function () {
    $controller = Flight::generoController();
    $controller->getAll();
});
