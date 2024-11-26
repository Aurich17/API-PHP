<?php
namespace App\Models;

class ListaUserRequest {
    public $name_user;

    public function __construct($name_user) {
        $this->name_user = $name_user;
    }
}
?>