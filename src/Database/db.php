<?php
// Cargar el autoloader de Composer
require '../vendor/autoload.php';  // Asegúrate de que la ruta es correcta

// Usar Dotenv para cargar las variables de entorno
use Dotenv\Dotenv;

// Cargar las variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtener las variables del archivo .env
// $host = $_ENV['DB_HOST'];
// $port = $_ENV['DB_PORT'];
// $dbname = $_ENV['DB_NAME'];
// $username = $_ENV['DB_USER'];
// $password = $_ENV['DB_PASSWORD']; 

$host = 'localhost';
$port = '3306';
$dbname = 'tienda_repuestos';
$username = 'root';
$password = ''; 

// Crear la cadena de conexión PDO
$dsn = "mysql:host=$host;port=$port;dbname=$dbname";

// Opciones para PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Intentar conectar a la base de datos
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die($e);
}
?>