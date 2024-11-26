<?php
namespace App\Controllers;

use App\Services\AuthService;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DI\Container;
use Exception;

class AuthController {
    private $authService;

    public function __construct(Container $container) {
        $this->authService = new AuthService($container->get('pdo'));
    }

    // Ruta de login
    public function login(Request $request, Response $response, $args)
    {
        $params = $request->getParsedBody();
    
        // Validar parámetros
        if (empty($params['username']) || empty($params['password']) || empty($params['recaptcha_token'])) {
            $response->getBody()->write(json_encode(['error' => 'Faltan datos obligatorios']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        try {
            // Validar reCAPTCHA
            $recaptchaResponse = $this->authService->validateRecaptchaToken($params['recaptcha_token'], 'login_action');
            if (!$recaptchaResponse['success'] || $recaptchaResponse['score'] < 0.5) {
                $response->getBody()->write(json_encode(['error' => 'Falló la validación de reCAPTCHA']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
    
            // Autenticar usuario
            $token = $this->authService->authenticateUser($params['username'], $params['password']);
    
            $response->getBody()->write(json_encode(['access_token' => $token]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    
        } catch (Exception $e) {
            error_log('Error de login: ' . $e->getMessage());
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    // Ruta para obtener el perfil del usuario
    public function profile(Request $request, Response $response, $args) {
        $token = $request->getHeader('Authorization')[0]; // Obtener el token del encabezado Authorization
        if (!$token) {
            $body = json_encode(['error' => 'Token no proporcionado']);
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write($body);

            return $response->withStatus(400); // Estado 400 para error
        }

        try {
            // Llamar al servicio para obtener el perfil
            $profile = $this->authService->getProfile($token);
            $body = json_encode($profile);
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write($body);

            return $response->withStatus(200); // Estado 200 para éxito
        } catch (Exception $e) {
            $body = json_encode(['error' => $e->getMessage()]);
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write($body);

            return $response->withStatus(400); // Estado 400 para error
        }
    }
}