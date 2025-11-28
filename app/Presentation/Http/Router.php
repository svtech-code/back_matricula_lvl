<?php

namespace App\Presentation\Http;

class Router
{
    public static function loadRoutes(): void
    {
        $routesPath = __DIR__ . "/Routes";
        
        if (file_exists("$routesPath/auth.php")) {
            require_once "$routesPath/auth.php";
        }
        
        if (file_exists("$routesPath/familiar.php")) {
            require_once "$routesPath/familiar.php";
        }
        
        if (file_exists("$routesPath/genero.php")) {
            require_once "$routesPath/genero.php";
        }
        
        if (file_exists("$routesPath/escolaridad.php")) {
            require_once "$routesPath/escolaridad.php";
        }
        
        if (file_exists("$routesPath/formacion-general-opcion.php")) {
            require_once "$routesPath/formacion-general-opcion.php";
        }
        
        if (file_exists("$routesPath/tipo-familiar.php")) {
            require_once "$routesPath/tipo-familiar.php";
        }
        
        if (file_exists("$routesPath/ficha-matricula.php")) {
            require_once "$routesPath/ficha-matricula.php";
        }
        
        if (file_exists("$routesPath/matricula.php")) {
            require_once "$routesPath/matricula.php";
        }
    }
}
