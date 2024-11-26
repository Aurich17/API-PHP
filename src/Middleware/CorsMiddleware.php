<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

class CorsMiddleware
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        // Agregar los encabezados CORS necesarios
        $response = $response->withHeader('Access-Control-Allow-Origin', '*')
                             ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                             ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        // Si es una petición OPTIONS, no procesar más
        if ($request->getMethod() === 'OPTIONS') {
            return $response;
        }

        // Pasar la solicitud a la siguiente capa
        return $next($request, $response);
    }
}