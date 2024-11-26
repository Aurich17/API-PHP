<?php
// src/Controllers/TiposController.php

namespace App\Controllers;

use App\Services\TiposService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TiposController {
    private $tiposService;
    public $requestTipo;

    public function __construct($pdo) {
        if ($pdo == null) {
            throw new \Exception("No se pudo establecer la conexión a la base de datos.");
        }
        $this->tiposService = new TiposService($pdo);
    }

    // Método para listar los tipos
    public function listarTipos(Request $request, Response $response, $args) {
        // Obtener los parámetros del cuerpo de la solicitud
        $params = $request->getParsedBody();
        error_log("LLEGA A LA FUNCION parametros");
        // Convertir el array $params a JSON para imprimirlo correctamente en los logs
        error_log("Cuerpo de la solicitud: " . var_export($params, true));
        error_log(json_encode($params));
        // Crear un objeto con los parámetros recibidos
        $requestTipo = (object) [
            'tabla_tab' => $params['tabla_tab'] ?? '',
            'desc_tipos' => $params['desc_tipos'] ?? ''
        ];
    
        try {
            // Llamar al servicio para obtener los tipos
            $result = $this->tiposService->ListaTipos($requestTipo);
            // Devolver la respuesta en formato JSON
            $response->getBody()->write(json_encode($result));
        } catch (\Exception $e) {
            // Manejo de errores
            $data = [
                'error' => 'No se pudo gestionar el tipo',
                'details' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($data));
        }
    
        return $response->withHeader('Content-Type', 'application/json');
    }
    //METODO PARA INSERTAR TIPOS
    public function insertTipos(Request $request, Response $response, $args) {
        // Obtener los parámetros del cuerpo de la solicitud
        $params = $request->getParsedBody();
        $requestTipo = (object) [
            'accion' => $params['accion'] ?? '',
            'p_id_tipo' => $params['p_id_tipo'] ?? '',
            'p_tab_tabla' => $params['p_tab_tabla'] ?? '',
            'p_des_tipo' => $params['p_des_tipo'] ?? '',
            'p_cod_tipo' => $params['p_cod_tipo'] ?? ''
        ];
        $result = $this->tiposService->insertTipos($requestTipo);
            // Devolver la respuesta en formato JSON
            $response->getBody()->write(json_encode($result));
    
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>