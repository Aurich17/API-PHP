<?php
// src/Routes/UserRoutes.php

namespace App\Routes;

use App\Controllers\UserController;
use Slim\App;

class UserRoutes {
    public function __construct(App $app, $pdo) {
        // Instanciamos el controlador de usuarios
        $controller = new UserController($pdo);

        // Ruta para listar los usuarios
        $app->post('/api/user', [$controller, 'listarUsuarios']);
        $app->post('/api/usuario/manage', [$controller, 'manageUser']);
        $app->post('/api/usuario/newUser', [$controller, 'insertUser']);
    }
}
?>