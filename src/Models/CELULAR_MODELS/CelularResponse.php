<?php
    namespace App\Models\CELULAR_MODELS;

    class CelularResponse {
        public $id_celular;
        public $cantidad;
        public $marca;
        public $cod_marca;
        public $modelo;
        public $precio_completo;
        public $descripcion;
        public $fecha_agregado;
        public $imagen;
        public $partes = [];
    
        public function __construct($id_celular,$cantidad,$marca,$cod_marca, $modelo, $precio_completo, $descripcion, $fecha_agregado, $imagen) {
            $this->id_celular = $id_celular;
            $this->cantidad = $cantidad;
            $this->marca = $marca;
            $this->cod_marca = $cod_marca;
            $this->modelo = $modelo;
            $this->precio_completo = $precio_completo;
            $this->descripcion = $descripcion;
            $this->fecha_agregado = $fecha_agregado;
            $this->imagen = $imagen;
        }
    }
?>
