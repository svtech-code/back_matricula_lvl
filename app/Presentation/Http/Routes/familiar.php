<?php

Flight::route('GET /api/familiares/@rut:[0-9]+', function ($rut) {
    $controller = Flight::familiarController();
    $controller->getByRut((int)$rut);
});
