<?php
namespace App\Models\CELULAR_MODELS;

class WishListRequest {
    public $p_id_usuario;
    public $p_id_celular;
    public $p_deseado;

    public function __construct($id_usuario, $id_celular, $deseado) {
        $this->p_id_usuario = $id_usuario;
        $this->p_id_celular = $id_celular;
        $this->p_deseado = $deseado;
    }
}
?>