<?php

Flight::route('POST /api/auth/login', function () {
    $controller = Flight::authController();
    $controller->login();
});
