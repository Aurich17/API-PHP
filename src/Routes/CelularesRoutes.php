<?php
    // src/Routes/CelularesRoutes.php

    namespace App\Routes;

    use App\Controllers\CelularesController;
    use Slim\App;

    class CelularesRoutes {
        public function __construct(App $app, $pdo) {
            
            $controller = new CelularesController($pdo);
            $app->post('/api/celulares', [$controller, 'getCelulares']);
            $app->post('/api/gestionaCelular', [$controller, 'gestionaCelular']);
            $app->post('/api/gestionaWishList', [$controller, 'manageWishList']);
            $app->post('/api/listaWishList', [$controller, 'listaWishList']);
        }
    }
?>