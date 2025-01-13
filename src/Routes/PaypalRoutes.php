<?php
// src/Routes/PaypalRoutes.php

namespace App\Routes;

use App\Controllers\PaypalController;
use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PaypalRoutes {
    public function __construct(App $app, $pdo) {
        $controller = new PaypalController($pdo);
        $app->post('/api/paypal/create-order', [$controller, 'createOrder']);
        $app->post('/api/paypal/capture-order', [$controller, 'captureOrder']);
        $app->get('/api/paypal-client-id', function (Request $request, Response $response) {
            // $clientId = 'Afic_j4GJSEhbLhjUO2jHvutPBozdL8r7PMTwNcliJ1x9jWt1SeOF9vp8MnoF39eKkslYFparSwh_ehM'; // DESARROLLO
            $clientId = 'Ae1o-swz1Iar3UYyFbKT3Qn5Bh9vZTpCb4sM_kIutO0ojmdMGPAYj-ZozcL37lZjXRdWxEp741sgIXui'; // PRODUCCION
            $response->getBody()->write(json_encode(['client_id' => $clientId]));
            return $response->withHeader('Content-Type', 'application/json');
        });
    }
}
?>
