<?php
namespace App\Services;

use App\Models\USER_MODELS\UserResponse; // Suponiendo que tienes esta clase en tu carpeta Models
use PDO;

class UserService {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listarUsuarios($nameUser) {
        // Log para verificar los parámetros de entrada
        error_log("Buscando usuarios con el nombre: $nameUser");

        $sql = "CALL ListarUsuarios(:name_user)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['name_user' => $nameUser]);

        $usuarios = [];

        // Log para verificar la consulta
        error_log("Ejecutando consulta: $sql");

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Crear un objeto de respuesta de usuario
            $usuarios[] = new UserResponse(
                $row['id_user'],
                $row['username'],
                $row['email'],
                $row['is_admin'],
                $row['created_at'],
                $row['updated_at'],
                $row['nombre_completo'],
                $row['doi_tag'],
                $row['doi_cod'],
                $row['doi_number'],
                $row['nacionalidad_cod'],
                $row['nacionalidad_tag']
            );
        }

        // Log para verificar si se encontraron usuarios
        error_log("Usuarios encontrados: " . print_r($usuarios, true));

        return $usuarios; // Devolver el array con los usuarios
    }

    public function manageUser($requestUser) {
        // Log para verificar los parámetros de entrada
        error_log("Gestionando usuario con ID: " . $requestUser->user_id);

        $sql = "CALL ManageUser(:accion, :user_id, :username, :email, :password, :is_admin, :nom_completo, :doi_cod, :num_doi, :nac_cod)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'accion' => $requestUser->accion,
            'user_id' => $requestUser->user_id,
            'username' => $requestUser->username,
            'email' => $requestUser->email,
            'password' => $requestUser->password,
            'is_admin' => $requestUser->is_admin,
            'nom_completo' => $requestUser->nom_completo,
            'doi_cod' => $requestUser->doi_cod,
            'num_doi' => $requestUser->num_doi,
            'nac_cod' => $requestUser->nac_cod
        ]);

        // Log para verificar el resultado
        error_log("Resultado de la gestión de usuario: " . print_r($stmt->rowCount(), true));

        return $stmt->rowCount(); // Retorna el número de filas afectadas (si se modificó un usuario, por ejemplo)
    }

    public function insertUser($requestUser) {
        // Log para verificar los parámetros de entrada
        $sql = "CALL InsertUser(:p_username, :p_email, :p_password, :p_doi_cod, :p_doi_number)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'p_username' => $requestUser->p_username,
            'p_email' => $requestUser->p_email,
            'p_password' => $requestUser->p_password,
            'p_doi_cod' => $requestUser->p_doi_cod,
            'p_doi_number' => $requestUser->p_doi_number
        ]);
        return $stmt->rowCount(); // Retorna el número de filas afectadas (si se modificó un usuario, por ejemplo)
    }
}
?>