<?php
namespace App\Routes;

use App\Controllers\AuthController;
use Slim\App;

class AuthRoutes {
    public function __construct(App $app) {
        $controller = new AuthController($app->getContainer());

        // Ruta para login
        $app->post('/api/login', [$controller, 'login']);

        // Ruta para obtener perfil de usuario
        $app->get('/api/users/profile', [$controller, 'profile']);
    }
}
?>