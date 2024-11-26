<?php
namespace App\Models\CELULAR_MODELS;
use App\Models\CELULAR_MODELS\PartesRequest;


class CelularesRequest {
    public string $p_accion;
    public string $p_marca_cod;
    public string $p_modelo;
    public int $p_cantidad;
    public float $p_precio_completo;
    public string $p_descripcion;
    public string $p_imagen;
    public array $p_partes;
    public int $p_celular_id;

    public function __construct($p_accion,$p_marca_cod, $p_modelo, $p_cantidad, $p_precio_completo, $p_descripcion, $p_imagen, $p_partes,$p_celular_id) {
        $this->p_accion = $p_accion;
        $this->p_marca_cod = $p_marca_cod;
        $this->p_modelo = $p_modelo;
        $this->p_cantidad = $p_cantidad;
        $this->p_precio_completo = $p_precio_completo;
        $this->p_descripcion = $p_descripcion;
        $this->p_imagen = $p_imagen;
        $this->p_partes = $p_partes;
        $this->p_celular_id =$p_celular_id;
    }
}
?>
