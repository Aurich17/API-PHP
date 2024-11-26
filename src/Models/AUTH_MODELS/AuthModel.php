<?php
    namespace App\Models\AUTH_MODELS;

    class TiposResponse{
        public $id;
        public $username;
        public $email;
        public $password;
        public $is_admin;
    
        public function __construct($id, $username, $email, $password,$is_admin) {
            $this->id = $id;
            $this->username = $username;
            $this->email = $email;
            $this->password = $password;
            $is_admin->is_admin = $is_admin;
        }
    }
?>
