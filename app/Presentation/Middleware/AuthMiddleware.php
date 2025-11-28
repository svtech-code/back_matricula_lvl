<?php

namespace App\Presentation\Middleware;

class AuthMiddleware
{
    public static function getTokenFromHeader(): ?string
    {
        $headers = getallheaders();
        
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }
}
