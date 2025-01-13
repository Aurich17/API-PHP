<?php
namespace App\Services;

use Firebase\JWT\JWT;
use App\Models\AUTH_MODELS\AuthModel; // Suponiendo que tienes esta clase en tu carpeta Models
use Psr\Http\Message\ServerRequestInterface;
use PDO;
use Exception;
use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;

class AuthService {
    private $pdo;
    private $jwtKey = '2024PASSWORDREPUESTOSCELULARES2024'; // Cambia esto por una clave secreta
    private $secretKey = '6LdHwYcqAAAAAOyzMft5mh29WPRMts8impm432tO';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Verifica la contraseña del usuario
    public function verifyPassword($inputPassword, $storedPassword) {
        return password_verify($inputPassword, $storedPassword);
    }

    // Genera un token JWT para el usuario
    public function encodeToken($payload) {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;  // El token expirará en 1 hora
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $expirationTime;

        return JWT::encode($payload, $this->jwtKey, 'HS256');
    }

    // Decodifica un token JWT y verifica su validez
    public function decodeToken($token) {
        try {
            // Separar el JWT en sus partes
            list($header, $payload, $signature) = explode('.', $token);
    
            // Decodificar la parte del payload (base64)
            $decodedPayload = json_decode(base64_decode(str_pad(strtr($payload, '-_', '+/'), strlen($payload) % 4, '=', STR_PAD_RIGHT)), true);
    
            return $decodedPayload; // Retorna el payload decodificado
        } catch (Exception $e) {
            throw new Exception("Error al decodificar el token: " . $e->getMessage());
        }
    }

    // Función para realizar el login
    public function login(ServerRequestInterface $request, Response $response, $args)
    {
        $params = $request->getParsedBody();
        error_log(json_encode($params)); // Verificar los datos recibidos
    
        // Validar parámetros
        if (empty($params['username']) || empty($params['password']) || empty($params['recaptcha_token'])) {
            error_log('INGRESA AQUI ========================>');
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(400)
                            ->write(json_encode(['error' => 'Faltan datos obligatorios']));
        }
    
        $username = $params['username'];
        $password = $params['password'];
        $recaptchaToken = $params['recaptcha_token'];
    
        // Validar el reCAPTCHA
        $recaptchaResponse = $this->verifyRecaptcha($recaptchaToken);
        if (!$recaptchaResponse['success'] || $recaptchaResponse['score'] < 0.5) {
            error_log('Falló la validación de reCAPTCHA: ' . json_encode($recaptchaResponse));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(400)
                            ->write(json_encode(['error' => 'Falló la validación de reCAPTCHA']));
        }
    
        try {
            // Autenticar usuario y generar el token
            $token = $this->authenticateUser($username, $password);
    
            // Crear respuesta con el token
            $responseBody = json_encode(['access_token' => $token]);
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200)
                            ->write($responseBody);
        } catch (Exception $e) {
            error_log('Error de login: ' . $e->getMessage());
            $responseBody = json_encode(['error' => $e->getMessage()]);
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(400)
                            ->write($responseBody);
        }
    }
    
    public function validateRecaptchaToken(string $token): array
    {
        $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
        //LOCAL
        // $recaptchaSecret = '6LfHMYsqAAAAAKY9PfYX2o5bwNw1clD-Wgx9w1my';
        //produccion
        $recaptchaSecret = '6LcAQYsqAAAAANJm0DNhPWQvYbQBHErRLvXkgFnK';

        $response = file_get_contents($recaptchaUrl . '?secret=' . $recaptchaSecret . '&response=' . $token);
        return json_decode($response, true);
    }
    private function verifyRecaptcha($token) {
        //LOCAL
        //$secretKey = '6LfHMYsqAAAAAKY9PfYX2o5bwNw1clD-Wgx9w1my'; // Sustituye por tu clave secreta
        //PRODUCCION
        $secretKey = '6LcAQYsqAAAAANJm0DNhPWQvYbQBHErRLvXkgFnK'; // Sustituye por tu clave secreta

        $url = 'https://www.google.com/recaptcha/api/siteverify';
    
        $data = [
            'secret' => $secretKey,
            'response' => $token
        ];
    
        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];
    
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
    
        return json_decode($result, true);
    }

    // Función para obtener el perfil de usuario
    public function getProfile($token) {
        $payload = $this->decodeToken($token);

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $payload['username']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Usuario no encontrado");
        }

        return [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'is_admin' => $user['is_admin'],
            'nombre_completo' => $user['nombre_completo'],
            'doi_cod' => $user['doi_cod'],
            'doi_number' => $user['doi_number'],
            'nacionalidad_cod' => $user['nacionalidad_cod']
        ];
    }

    public function authenticateUser(string $username, string $password): string
    {
        // Verificar si la conexión PDO está inicializada
        if ($this->pdo === null) {
            error_log('La conexión a la base de datos no está inicializada');
            throw new Exception('Conexión a la base de datos no inicializada');
        }

        // Consultar la base de datos para encontrar al usuario
        error_log('LLEGA HASTA AQUI');
        $query = "SELECT id, username, password FROM users WHERE username = :username";
        
        try {
            $stmt = $this->pdo->prepare($query); // Usamos la conexión PDO proporcionada
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar si el usuario existe y si la contraseña es válida
            if (!$user || $user['password'] !== hash('sha256', $password)) {
                throw new Exception('Credenciales inválidas');
            }

            // Generar el token JWT
            $secretKey = 'DEVELOPERAURICH'; // Cambiar a una clave segura
            $expirationTime = time() + 3600; // Validez de 1 hora

            $payload = [
                'sub' => $user['id'],       // ID del usuario
                'username' => $user['username'],
                'iat' => time(),            // Tiempo de emisión
                'exp' => $expirationTime,   // Tiempo de expiración
            ];

            return JWT::encode($payload, $secretKey, 'HS256');
        } catch (PDOException $e) {
            // Manejar errores de la base de datos
            error_log('Error al ejecutar la consulta: ' . $e->getMessage());
            throw new Exception('Error al autenticar al usuario');
        }
    }
}