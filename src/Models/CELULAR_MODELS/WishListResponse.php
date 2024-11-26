<?php
    namespace App\Models\CELULAR_MODELS;

    class WishListResponse {
        public $id_celular;
        public $modelo;
        public $en_lista_deseos;
        public $imagen;
    
        public function __construct($id_celular,$modelo,$en_lista_deseos,$imagen) {
            $this->id_celular = $id_celular;
            $this->modelo = $modelo;
            $this->en_lista_deseos = $en_lista_deseos;
            $this->imagen = $imagen;
        }
    }
?>
