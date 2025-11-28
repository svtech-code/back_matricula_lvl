<?php

use App\Presentation\Http\Router;

// Carga de rutas
Router::loadRoutes();

// Ruta sin protección
Flight::route("GET /", function () {
    Flight::json([
        "success" => true,
        "data" => [
            "message" => "API Matrícula - Backend",
            "version" => "1.0.0",
            "status" => "active",
            "endpoints" => [
                "GET /" => "Información de la API",
                "GET /health" => "Health check",
                "POST /api/auth/login" => "Autenticación de usuarios",
                "GET /api/escolaridades" => "Obtener catálogo de escolaridades",
                "GET /api/familiares/:rut" => "Obtener familiar por RUT",
                "POST /api/ficha-matricula" => "Crear ficha de matrícula completa",
                "GET /api/ficha-matricula/verificar" => "Verificar prematrícula por RUT",
                "GET /api/formacion-general-opciones" => "Obtener opciones de formación general",
                "GET /api/generos" => "Obtener catálogo de géneros",
                "GET /api/matriculas" => "Obtener todas las matrículas",
                "GET /api/matriculas/:id" => "Obtener una matrícula por ID",
                "POST /api/matriculas" => "Crear nueva matrícula",
                "PUT /api/matriculas/:id" => "Actualizar matrícula",
                "GET /api/tipos-familiares" => "Obtener catálogo de tipos de familiares"
            ]
        ]
    ]);
});


// Las rutas se cargan con loadeerRoutes
// No duplciar rutas aquí, para evitar conflictos de "mothod not allowed"
