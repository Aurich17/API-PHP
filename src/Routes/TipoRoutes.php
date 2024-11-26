<?php
namespace App\Routes;

use App\Controllers\TiposController;
use Slim\App;

class TipoRoutes {
    public function __construct(App $app, $pdo) {
        // Instanciamos el controlador de usuarios
        $controller = new TiposController($pdo);
        $app->post('/api/tipos', [$controller, 'listarTipos']);
        $app->post('/api/insertTipos', [$controller, 'insertTipos']);
        // Log para verificar que la ruta está registrada
    }
}
?>