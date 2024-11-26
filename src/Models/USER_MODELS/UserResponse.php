<?php
    namespace App\Models\USER_MODELS;

    class UserResponse{
        public $id_user;
        public $username;
        public $email;
        public $is_admin;
        public $created_at;
        public $updated_at;
        public $nombre_completo;
        public $doi_tag;
        public $doi_cod;
        public $doi_number;
        public $nacionalidad_cod;
        public $nacionalidad_tag;
    
        public function __construct($id_user, $username, $email, $is_admin, $created_at, $updated_at, $nombre_completo,$doi_tag,$doi_cod,$doi_number,$nacionalidad_cod,$nacionalidad_tag) {
            $this->id_user = $id_user;
            $this->username = $username;
            $this->email = $email;
            $this->is_admin = $is_admin;
            $this->created_at = $created_at;
            $this->updated_at = $updated_at;
            $this->nombre_completo = $nombre_completo;
            $this->doi_tag = $doi_tag;
            $this->doi_cod = $doi_cod;
            $this->doi_number = $doi_number;
            $this->nacionalidad_cod = $nacionalidad_cod;
            $this->nacionalidad_tag = $nacionalidad_tag;
        }
    }
?>
