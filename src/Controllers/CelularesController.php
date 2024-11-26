<?php
// src/Controllers/CelularesController.php

namespace App\Controllers;

use App\Services\CelularesService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\CELULAR_MODELS\CelularesRequest;
use App\Models\CELULAR_MODELS\WishListRequest;

class CelularesController {
    private $celularesService;

    public function __construct($pdo) {
        if ($pdo == null) {
            throw new \Exception("No se pudo establecer la conexión a la base de datos.");
        }
        $this->celularesService = new CelularesService($pdo);
    }

    // Método que maneja la solicitud de los celulares
    public function getCelulares(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();
        $namePhone = $params['name_phone'] ?? '';

        try {
            $celulares = $this->celularesService->listarCelulares($namePhone);
            $data = $celulares;
            $response->getBody()->write(json_encode($data));
        } catch (\Exception $e) {
            $data = ["error" => "No se pudieron obtener los celulares", "details" => $e->getMessage()];
            $response->getBody()->write(json_encode($data));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    // Método para insertar un celular
    public function gestionaCelular(Request $request, Response $response) {
        $params = $request->getParsedBody();
        $p_accion = $params['p_accion'];
        $p_marca_cod = $params['p_marca_cod'];
        $p_modelo = $params['p_modelo'];
        $p_cantidad = (int)$params['p_cantidad'];
        $p_precio_completo = (float)$params['p_precio_completo'];
        $p_descripcion = $params['p_descripcion'];
        $p_imagen = isset($params['p_imagen']) ? $params['p_imagen'] : null;
        $p_partes = is_array($params['p_partes']) ? $params['p_partes'] : (is_null($params['p_partes']) ? [] : json_decode($params['p_partes'], true));
        $p_celular_id = $params['p_celular_id'];

        $celularRequest = new CelularesRequest(
            $p_accion,
            $p_marca_cod,
            $p_modelo,
            $p_cantidad,
            $p_precio_completo,
            $p_descripcion,
            $p_imagen,
            $p_partes,
            $p_celular_id
        );

        try {
            $result = $this->celularesService->gestionaCelular($celularRequest);
            $response->getBody()->write(json_encode($result));
        } catch (\Exception $e) {
            $error = ["error" => "No se pudo insertar el celular", "details" => $e->getMessage()];
            $response->getBody()->write(json_encode($error));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    //METODO PARA AGREGAR CELULAR A LA LISTA DE DESEOS
    public function manageWishList(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();
        $p_id_usuario = $params['p_id_usuario'] ?? '';
        $p_id_celular = $params['p_id_celular'] ?? '';
        $p_deseado = $params['p_deseado'] ?? '';

        $wishListRequest = new WishListRequest(
            $p_id_usuario,
            $p_id_celular,
            $p_deseado
        );
        try {
            $whistList = $this->celularesService->gestionaWishList($wishListRequest);
            $data = $whistList;
            $response->getBody()->write(json_encode($data));
        } catch (\Exception $e) {
            $data = ["error" => "No se pudieron obtener los celulares", "details" => $e->getMessage()];
            $response->getBody()->write(json_encode($data));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    //PARA LISTAR LA LISTA DE DESEOS POR USUARIO
    public function listaWishList(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();
        $idUsuario = $params['p_id_usuario'] ?? '';

        try {
            $wishList = $this->celularesService->listaWishList($idUsuario);
            $data = $wishList;
            $response->getBody()->write(json_encode($data));
        } catch (\Exception $e) {
            $data = ["error" => "No se pudieron obtener los celulares", "details" => $e->getMessage()];
            $response->getBody()->write(json_encode($data));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>
