<?php
    namespace App\Models\TIPOS_MODELS;

    class TiposResponse{
        public $id_tipo;
        public $tab_tabla;
        public $des_tipo;
        public $cod_tipo;
    
        public function __construct($id_tipo, $tab_tabla, $des_tipo, $cod_tipo) {
            $this->id_tipo = $id_tipo;
            $this->tab_tabla = $tab_tabla;
            $this->des_tipo = $des_tipo;
            $this->cod_tipo = $cod_tipo;
        }
    }
?>
