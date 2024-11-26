<?php
namespace App\Models;

class TiposRequest {
    public $tabla_tab;
    public $desc_tipos;

    public function __construct($tabla_tab,$desc_tipos) {
        $this->tabla_tab = $name_user;
        $this->desc_tipos = $desc_tipos;
    }
}
?>