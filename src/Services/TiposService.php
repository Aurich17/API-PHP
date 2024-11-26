<?php
namespace App\Services;

use App\Models\TIPOS_MODELS\TiposResponse; // Suponiendo que tienes esta clase en tu carpeta Models
use PDO;

class TiposService {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function ListaTipos($requestTipo) {
        $sql = "CALL ListaTipos(:tabla_tab, :desc_tipos)";
        $stmt = $this->pdo->prepare($sql);

        // Log para verificar los valores de entrada
        error_log("Valores de entrada: tabla_tab = " . $requestTipo->tabla_tab . ", desc_tipos = " . $requestTipo->desc_tipos);

        // Ejecutar la consulta con los datos recibidos
        $stmt->execute([
            'tabla_tab' => $requestTipo->tabla_tab ?? null,
            'desc_tipos' => $requestTipo->desc_tipos ?? null
        ]);

        $tipos = [];

        // Obtener los resultados de la consulta
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Crear un objeto TiposResponse con los datos obtenidos
            $tipos[] = new TiposResponse(
                $row['id_tipo'],
                $row['tab_tabla'],
                $row['des_tipo'],
                $row['cod_tipo']
            );
        }

        // Log para verificar los datos obtenidos
        error_log("Tipos encontrados: " . print_r($tipos, true));

        return $tipos; // Retornar los datos en formato array
    }

    public function InsertTipos($requestTipo) {
        $sql = "CALL ManageTipo(:accion,:p_id_tipo,:p_tab_tabla, :p_des_tipo, :p_cod_tipo)";
        $stmt = $this->pdo->prepare($sql);
        // Ejecutar la consulta con los datos recibidos
        $params = [
            'accion' => $requestTipo->accion ?? null,
            'p_id_tipo' => $requestTipo->p_id_tipo ?? null,
            'p_tab_tabla' => $requestTipo->p_tab_tabla ?? null,
            'p_des_tipo' => $requestTipo->p_des_tipo ?? null,
            'p_cod_tipo' => $requestTipo->p_cod_tipo ?? null
        ];
        $stmt->execute($params);
        return 'OK'; // Retornar los datos en formato array
    }
}
?>