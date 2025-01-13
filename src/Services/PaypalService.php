<?php
namespace App\Services;

use GuzzleHttp\Client;

class PaypalService {
    private $client;
    private $clientId;
    private $clientSecret;
    private $baseUrl;

    public function __construct() {
        $this->clientId = 'Ae1o-swz1Iar3UYyFbKT3Qn5Bh9vZTpCb4sM_kIutO0ojmdMGPAYj-ZozcL37lZjXRdWxEp741sgIXui'; //PRODUCCION
        $this->clientSecret = 'EOXiJ5NgcYj1_maZjKwPds4XBYvcSam2u41BF_qgCB5sunh0F1cq5tYtN1bxPA3eciCjfCXn2M1HGwB2'; //PRODUCCION
        $this->baseUrl = 'https://api-m.paypal.com'; // PRODUCCIÓN
        // $this->clientId = 'Afic_j4GJSEhbLhjUO2jHvutPBozdL8r7PMTwNcliJ1x9jWt1SeOF9vp8MnoF39eKkslYFparSwh_ehM'; // DESARROLLO
        // $this->clientSecret = 'EP6ZjqEEerJlh7fL_xdgyzXRieCRXaay_szsImiH101Nck77TstFrXnO_4RRQa1pq6s-uSyAxbLkJHEd'; // DESARROLLO 
        // $this->baseUrl = 'https://api-m.sandbox.paypal.com'; //DESARROLLO
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 10.0,
            'verify' => false,  
        ]);
    }

    // public function createOrder($amount, $currency = 'USD') {
    //     error_log("LLEGA AL SERVICE");
    //     // Validación del monto
    //     if ($amount <= 0) {
    //         error_log("Error: El monto debe ser mayor que cero. Monto recibido: $amount");
    //         throw new \Exception("El monto debe ser mayor que cero.");
    //     }
    
    //     try {
    //         // Obtener el token de acceso
    //         error_log("Obteniendo token de acceso...");
    //         $token = $this->getAccessToken();
    //         if (!$token) {
    //             error_log("Error: No se pudo obtener el token de acceso.");
    //             throw new \Exception("Error al obtener el token de acceso.");
    //         }
    //         error_log("Token de acceso obtenido correctamente.");
    
    //         // Realizar la solicitud a la API de PayPal
    //         error_log("Creando la orden en PayPal con el monto: $amount $currency");
    //         $response = $this->client->post('/v2/checkout/orders', [
    //             'headers' => [
    //                 'Authorization' => "Bearer $token",
    //                 'Content-Type' => 'application/json',
    //             ],
    //             'json' => [
    //                 'intent' => 'CAPTURE',
    //                 'purchase_units' => [
    //                     [
    //                         'amount' => [
    //                             'currency_code' => $currency,
    //                             'value' => $amount,
    //                         ],
    //                     ],
    //                 ],
    //             ],
    //         ]);
    
    //         // Decodificar la respuesta
    //         error_log("Respuesta recibida de PayPal.");
    //         $order = json_decode($response->getBody(), true);
    
    //         // Verificar si la orden se creó correctamente
    //         if (isset($order['id'])) {
    //             error_log("Orden creada exitosamente. ID de la orden: " . $order['id']);
    //             return $order;
    //         } else {
    //             error_log("Error al crear la orden. Respuesta: " . json_encode($order));
    //             throw new \Exception('Error al crear la orden: ' . json_encode($order));
    //         }
    //     } catch (\Exception $e) {
    //         // Registrar errores en el log
    //         error_log('Error en createOrder: ' . $e->getMessage());
    //         throw $e;
    //     }
    // }

    public function createPayment($totalPrecio, $currency) {
        // Mensaje para verificar el precio recibido
        error_log("Precio recibido para crear el pago: " . $totalPrecio);
    
        if ($totalPrecio <= 0) {
            throw new \Exception("El precio total debe ser mayor que cero.");
        }
    
        $token = $this->getAccessToken();
        
        // Mensaje para verificar el token de acceso
        error_log("Token de acceso obtenido: " . $token);
    
        // Realizar la solicitud para crear la orden de pago
        try {
            $response = $this->client->post('/v2/checkout/orders', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => $currency,
                                'value' => $totalPrecio, // Asegúrate de que este valor sea mayor a cero
                            ],
                        ],
                    ],
                ],
            ]);
    
            // Decodificar la respuesta JSON
            $order = json_decode($response->getBody(), true);
    
            // Verificar si se obtuvo el Order ID
            error_log("Respuesta de PayPal: " . json_encode($order)); // Log completo de la respuesta
    
            if (isset($order['id'])) {
                // Mensaje para confirmar que se recibió el Order ID
                error_log("Orden de pago creada con éxito. Order ID: " . $order['id']);
                return $order['id'];
            } else {
                // Si no se encuentra el Order ID, loggear el error completo
                error_log("Error al crear la orden de pago. Respuesta completa: " . json_encode($order));
                throw new \Exception("Error al crear la orden de pago: " . $order['message']);
            }
    
        } catch (\Exception $e) {
            // Loggear el error si la solicitud a PayPal falla
            error_log("Error al realizar la solicitud a PayPal: " . $e->getMessage());
            throw new \Exception("Error al realizar la solicitud a PayPal: " . $e->getMessage());
        }
    }

    public function captureOrder($orderId) {
        $token = $this->getAccessToken();

        $response = $this->client->post("/v2/checkout/orders/{$orderId}/capture", [
            'headers' => [
                'Authorization' => "Bearer $token",
                'Content-Type' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    private function getAccessToken() {
        $response = $this->client->post('/v1/oauth2/token', [
            'auth' => [$this->clientId, $this->clientSecret],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        $body = json_decode($response->getBody(), true);
        if (isset($body['access_token'])) {
            return $body['access_token'];
        } else {
            throw new \Exception('Error al obtener el token de acceso: ' . json_encode($body));
        }
    }
}
?>
