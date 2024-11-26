<?php
    // Cargar el autoloader de Composer
    require '../vendor/autoload.php';
    require '../src/Database/db.php';
    require '../src/Routes/AuthRoutes.php';

    // Usar el AppFactory para crear la aplicación
    use Slim\Factory\AppFactory;
    use DI\Container;

    // Crear la aplicación Slim
    $container = new Container();
    AppFactory::setContainer($container);
    $app = AppFactory::create();

    // Registramos la conexión PDO ya existente en el contenedor
    $container->set('pdo', function () use ($pdo) {  // $pdo es la conexión que ya tienes en db.php
        return $pdo;  // Devuelve la instancia de PDO creada en db.php
    });
    // Registra tus dependencias (por ejemplo, el controlador AuthController)
    $container->set(App\Controllers\AuthController::class, function($container) {
        return new App\Controllers\AuthController($container);
    });

    // Crear la conexión PDO
    $app->add(new \Slim\Middleware\BodyParsingMiddleware());
    $app->options('/{routes:.+}', function ($request,$response) {
        return $response;
    });
    $app->add(function ($request, $handler) {
        $response = $handler->handle($request);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization');
    });

    // Manejar todas las solicitudes OPTIONS
    // Definir una ruta básica
    $app->get('/', function ($request, $response, $args) {
        // Crear la respuesta JSON y devolverla
        echo 'Versión de PHP: ' . phpversion();
        $data = ["message" => "¡API funcionando con Slim!"];
        $response->getBody()->write(json_encode($data));

        // Establecer el tipo de contenido como JSON
        return $response->withHeader('Content-Type', 'application/json');
    });
    
    
    new \App\Routes\CelularesRoutes($app, $pdo);
    new \App\Routes\UserRoutes($app, $pdo);
    new \App\Routes\TipoRoutes($app, $pdo);
    new \App\Routes\AuthRoutes($app, $pdo);
    
    $app->run();
?>