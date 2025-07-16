<?php

namespace App\Models;

use App\config\Database;
use PDO;

class Usuario
{
    private $conn;
    private $table = 'usuarios';

    // Propiedades del objeto
    public $id;
    public $nombre;
    public $email;
    public $password_hash;
    public $activo;
    public $rol_id;

    public function __construct()
    {
        // Obtiene la conexión de la base de datos usando el Singleton
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Crea un nuevo usuario en la base de datos.
     * Hashea la contraseña antes de guardarla.
     */
    public function crear()
    {
        $query = 'INSERT INTO ' . $this->table . ' (nombre, email, password_hash, rol_id, activo) VALUES (:nombre, :email, :password_hash, :rol_id, :activo)';

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->rol_id = htmlspecialchars(strip_tags($this->rol_id));
        $this->activo = true; // Por defecto, los nuevos usuarios están activos

        // Hashear la contraseña
        $this->password_hash = password_hash($this->password_hash, PASSWORD_BCRYPT);

        // Vincular datos
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password_hash', $this->password_hash);
        $stmt->bindParam(':rol_id', $this->rol_id);
        $stmt->bindParam(':activo', $this->activo, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    /**
     * Busca un usuario por su email.
     * Útil para el proceso de login.
     */
    public static function findByEmail($email)
    {
        $db = Database::getInstance()->getConnection();
        $query = 'SELECT * FROM usuarios WHERE email = :email LIMIT 1';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}