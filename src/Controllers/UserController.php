<?php
// src/Controllers/UserController.php

namespace App\Controllers;

use App\Services\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController {
    private $userService;

    public function __construct($pdo) {
        if ($pdo == null) {
            throw new \Exception("No se pudo establecer la conexión a la base de datos.");
        }
        $this->userService = new UserService($pdo);
    }

    // Método que maneja la solicitud de listado de usuarios
    public function listarUsuarios(Request $request, Response $response, $args) {
        // Obtener parámetros de la solicitud
        $params = $request->getParsedBody();
        $nameUser = $params['name_user'] ?? '';  // Nombre de usuario a buscar

        try {
            // Llamamos al servicio para obtener los usuarios
            $usuarios = $this->userService->listarUsuarios($nameUser);

            // Preparar respuesta con los datos de los usuarios
            $data = [];
            foreach ($usuarios as $usuario) {
                $data[] = [
                    'id_user' => $usuario->id_user,
                    'username' => $usuario->username,
                    'email' => $usuario->email,
                    'is_admin' => $usuario->is_admin,
                    'created_at' => $usuario->created_at,
                    'updated_at' => $usuario->updated_at,
                    'nombre_completo' => $usuario->nombre_completo,
                    'doi_tag' => $usuario->doi_tag,
                    'doi_cod' => $usuario->doi_cod,
                    'doi_number' => $usuario->doi_number,
                    'nacionalidad_cod' => $usuario->nacionalidad_cod,
                    'nacionalidad_tag' => $usuario->nacionalidad_tag
                ];
            }

            // Devolver la respuesta en formato JSON
            $response->getBody()->write(json_encode($data));
        } catch (\Exception $e) {
            // Si ocurre un error, devolver el mensaje de error en formato JSON
            $data = ["error" => "No se pudieron obtener los usuarios", "details" => $e->getMessage()];
            $response->getBody()->write(json_encode($data));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    // Método que maneja la solicitud para gestionar un usuario
    public function manageUser(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();

        $accion = $params['accion'];
        $user_id = $params['user_id'];
        $username = $params['username'];
        $email = $params['email'];
        $password = $params['password'];
        $is_admin = $params['is_admin'];
        $nom_completo = $params['nom_completo'];
        $doi_cod = $params['doi_cod'];
        $num_doi = $params['num_doi'];
        $nac_cod = $params['nac_cod'];

        try {
            // Crear objeto de solicitud de usuario
            $requestUser = (object)[
                'accion' => $accion,
                'user_id' => $user_id,
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'is_admin' => $is_admin,
                'nom_completo' => $nom_completo,
                'doi_cod' => $doi_cod,
                'num_doi' => $num_doi,
                'nac_cod' => $nac_cod
            ];

            // Llamamos al servicio para gestionar el usuario
            $result = $this->userService->manageUser($requestUser);

            // Si no se afectan filas, es posible que el usuario no haya sido gestionado correctamente
            if ($result === 0) {
                $data = ["error" => "No se realizó ninguna acción en el usuario."];
            } else {
                $data = ["success" => "Acción realizada correctamente en el usuario."];
            }

            // Devolver la respuesta en formato JSON
            $response->getBody()->write(json_encode($data));
        } catch (\Exception $e) {
            // Si ocurre un error, devolver el mensaje de error en formato JSON
            $data = ["error" => "No se pudo gestionar el usuario", "details" => $e->getMessage()];
            $response->getBody()->write(json_encode($data));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function insertUser(Request $request, Response $response, $args) {
        try {
            // Obtener los parámetros del cuerpo de la solicitud
            $params = $request->getParsedBody();
            $requestTipo = (object) [
                'p_username' => $params['p_username'] ?? '',
                'p_email' => $params['p_email'] ?? '',
                'p_password' => $params['p_password'] ?? '',
                'p_doi_cod' => $params['p_doi_cod'] ?? '',
                'p_doi_number' => $params['p_doi_number'] ?? ''
            ];
    
            // Intentar insertar el usuario
            $result = $this->userService->insertUser($requestTipo);
    
            // Si todo fue bien, preparar respuesta exitosa
            $responseBody = [
                'status' => 200,
                'message' => 'Usuario insertado correctamente.'
            ];
        } catch (\Exception $e) {
            // En caso de error, preparar respuesta de error
            $responseBody = [
                'status' => 500,
                'message' => 'Error al insertar el usuario: ' . $e->getMessage()
            ];
        }
    
        // Devolver la respuesta en formato JSON
        $response->getBody()->write(json_encode($responseBody));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($responseBody['status']);
    }
}
?>