<?php

// Carga de las dependencias
require_once __DIR__ . "/../vendor/autoload.php";

// Configuración ante cors
require_once __DIR__ . "/../config/cors-control.php";

require_once __DIR__ . "/../config/config.php";

// Configuración de rutas
require_once __DIR__ . "/../routes/api.php";

Flight::start();
