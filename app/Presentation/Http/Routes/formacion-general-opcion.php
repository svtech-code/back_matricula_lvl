<?php

Flight::route('GET /api/formacion-general-opciones', function () {
    $controller = Flight::formacionGeneralOpcionController();
    $controller->getAll();
});
