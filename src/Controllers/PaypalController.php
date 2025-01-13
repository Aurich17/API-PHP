<?php
// src/Controllers/PaypalController.php

namespace App\Controllers;

use App\Services\PaypalService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PaypalController {
    private $paypalService;

    public function __construct($pdo) {
        $this->paypalService = new PaypalService();
    }

    public function createOrder(Request $request, Response $response) {
        $params = $request->getParsedBody();
        $amount = $params['total'] ?? 0;

        error_log("Parametros recibidos: " . json_encode($params));
        
        try {
            error_log("Monto recibido para crear la orden: $amount USD");
            $order = $this->paypalService->createPayment($amount, 'USD');
            error_log("Respuesta de PayPal: " . json_encode($order));

            if ($order && isset($order)) {
                error_log("Orden creada exitosamente. ID de la orden: " . $order);
                $response->getBody()->write(json_encode(['orderId' => $order]));
            } else {
                error_log("Error: La respuesta de PayPal no contiene un ID de orden.");
                $error = ['error' => 'Error al crear la orden', 'details' => 'No se recibió un ID de orden válido.'];
                $response->getBody()->write(json_encode($error));
            }
        } catch (\Exception $e) {
            error_log('Error en createOrder: ' . $e->getMessage());
            $error = ['error' => 'No se pudo crear la orden', 'details' => $e->getMessage()];
            $response->getBody()->write(json_encode($error));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function captureOrder(Request $request, Response $response) {
        $params = $request->getParsedBody();
        $orderId = $params['orderId'] ?? '';

        try {
            $capture = $this->paypalService->captureOrder($orderId);
            $response->getBody()->write(json_encode($capture));
        } catch (\Exception $e) {
            $error = ['error' => 'No se pudo capturar la orden', 'details' => $e->getMessage()];
            $response->getBody()->write(json_encode($error));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>
