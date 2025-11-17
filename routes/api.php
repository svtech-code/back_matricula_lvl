<?php

use App\Presentation\Http\Router;

Router::loadRoutes();

Flight::route("GET /", function () {
    Flight::json([
        "success" => true,
        "data" => [
            "message" => "API Matrícula - Backend",
            "version" => "1.0.0",
            "status" => "active",
            "endpoints" => [
                "GET /" => "Información de la API",
                "POST /api/auth/login" => "Autenticación de usuarios"
            ]
        ]
    ]);
});
