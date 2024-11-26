<?php
    // src/Services/CelularesService.php

    namespace App\Services;

    use App\Models\CELULAR_MODELS\CelularResponse;
    use App\Models\CELULAR_MODELS\WishListResponse;
    use App\Models\CELULAR_MODELS\CelularesRequest;
    use App\Models\CELULAR_MODELS\WishListRequest;
    use PDO;

    class CelularesService {
        private $pdo;

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        public function listarCelulares($namePhone) {
            // Registrar el tiempo de inicio
            $start_time = microtime(true);
            error_log("Buscando celulares con el nombre: $namePhone");
        
            // Consulta SQL
            $sql = "CALL ListarCelularesConPartes(:name_phone)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['name_phone' => $namePhone]);
        
            // Traemos todos los resultados de una vez
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $celulares = [];
        
            // Log para verificar la consulta
            error_log("Ejecutando consulta: $sql");
        
            foreach ($rows as $row) {
                // Si el celular no existe en el array, lo agregamos
                if (!isset($celulares[$row['id_celular']])) {
                    $celulares[$row['id_celular']] = new CelularResponse(
                        $row['id_celular'],
                        $row['cantidad'],
                        $row['marca'],
                        $row['cod_marca'],
                        $row['modelo'],
                        $row['precio_completo'],
                        $row['descripcion'],
                        $row['fecha_agregado'],
                        $row['imagen']
                    );
                }
        
                // Si hay partes asociadas, las agregamos al array de partes
                if (!empty($row['nombre_parte'])) {
                    $parte = [
                        'nombre' => $row['nombre_parte'],
                        'cod_parte' => $row['cod_parte'],
                        'precio' => $row['precio_parte'],
                        'cantidad' => $row['cantidad']
                    ];
                    $celulares[$row['id_celular']]->partes[] = $parte;
                }
            }
        
            // Medir el tiempo después de procesar los resultados
            $end_time = microtime(true);
            $execution_time = $end_time - $start_time;
        
            // Log para el tiempo de ejecución total
            error_log("Tiempo de ejecución total: $execution_time segundos");
        
            // Devolver un array de celulares
            return array_values($celulares);
        }

        public function gestionaCelular(CelularesRequest $request) {
            // Crear el JSON de partes
            $partes_json = json_encode(array_map(function($parte) {
                if (is_object($parte)) {
                    return [
                        'componente_cod' => $parte->componente_cod,
                        'precio' => $parte->precio,
                        'cantidad' => $parte->cantidad,
                    ];
                } elseif (is_array($parte)) {
                    return [
                        'componente_cod' => $parte['componente_cod'],
                        'precio' => $parte['precio'],
                        'cantidad' => $parte['cantidad'],
                    ];
                }
                return [];
            }, $request->p_partes));
        
            // Consulta SQL
            $sql = "CALL gestionar_celular_con_partes(
                        :p_accion,
                        :p_marca_cod,
                        :p_modelo,
                        :p_cantidad,
                        :p_precio_completo,
                        :p_descripcion,
                        :p_imagen,
                        :p_partes,
                        :p_celular_id
                    )";
        
            // Intentar ejecutar la consulta
            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':p_accion' => $request->p_accion,
                    ':p_marca_cod' => $request->p_marca_cod,
                    ':p_modelo' => $request->p_modelo,
                    ':p_cantidad' => $request->p_cantidad,
                    ':p_precio_completo' => $request->p_precio_completo,
                    ':p_descripcion' => $request->p_descripcion,
                    ':p_imagen' => $request->p_imagen,
                    ':p_partes' => $partes_json,  // JSON válido
                    ':p_celular_id' => $request->p_celular_id,
                ]);
                return 'OK';
            } catch (PDOException $e) {
                return 'ERROR';
            }
        }

        public function gestionaWishList(WishListRequest $request) {
            // Consulta SQL para el procedimiento
            $sql = "CALL ManejarListaDeseos(
                :p_id_usuario,
                :p_id_celular,
                :p_deseado
            )";

            try {
                // Preparar la consulta
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':p_id_usuario' => $request->p_id_usuario,   // ID del usuario
                    ':p_id_celular' => $request->p_id_celular,   // ID del celular
                    ':p_deseado' => $request->p_deseado,         // Boolean: true (agregar), false (eliminar)
                ]);
                return 'OK';
            } catch (PDOException $e) {
                // Manejo de errores
                return 'ERROR: ' . $e->getMessage();
            }
        }

        public function listaWishList($p_id_usuario) {
            // Consulta SQL
            $sql = "CALL ObtenerListaDeseos(:p_id_usuario)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['p_id_usuario' => $p_id_usuario]);

            // Traemos todos los resultados de una vez
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $wishList = [];

            // Log para verificar la consulta
            error_log("Ejecutando consulta: $sql");

            foreach ($rows as $row) {
                $wishList[$row['id_celular']] = new WishListResponse(
                    $row['id_celular'],
                    $row['modelo'],
                    $row['en_lista_deseos'],
                    $row['imagen']
                );
            }
            return array_values($wishList);
        }
    }
?>