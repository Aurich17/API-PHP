<?php
namespace App\Models;

class UserManageRequest {
    public $p_username;
    public $p_email;
    public $p_password;
    public $p_doi_cod;
    public $p_doi_number;

    public function __construct($p_username, $p_email, $p_password, $p_doi_cod, $p_doi_number) {
        $this->p_username = $p_username;
        $this->p_email = $p_email;
        $this->p_password = $p_password;
        $this->p_doi_cod = $p_doi_cod;
        $this->p_doi_number = $p_doi_number;
    }
}
?>