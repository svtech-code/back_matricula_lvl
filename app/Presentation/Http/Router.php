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
    }
}
