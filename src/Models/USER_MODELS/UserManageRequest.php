<?php
namespace App\Models;

class UserManageRequest {
    public $accion;
    public $user_id;
    public $username;
    public $email;
    public $password;
    public $is_admin;
    public $nom_completo;
    public $doi_cod;
    public $num_doi;
    public $nac_cod;

    public function __construct($accion, $user_id, $username, $email, $password, $is_admin, $nom_completo, $doi_cod, $num_doi, $nac_cod) {
        $this->accion = $accion;
        $this->user_id = $user_id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->is_admin = $is_admin;
        $this->nom_completo = $nom_completo;
        $this->doi_cod = $doi_cod;
        $this->num_doi = $num_doi;
        $this->nac_cod = $nac_cod;
    }
}
?>