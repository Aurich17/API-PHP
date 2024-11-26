<?php
namespace App\Models\CELULAR_MODELS;

class PartesRequest {
    public string $componente_cod;
    public float $precio;
    public int $cantidad;

    public function __construct($componente_cod, $precio, $cantidad) {
        $this->componente_cod = $componente_cod;
        $this->precio = $precio;
        $this->cantidad = $cantidad;
    }
}
?>