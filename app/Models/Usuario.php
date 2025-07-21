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

    /**
     * Obtiene todos los usuarios administrativos de la base de datos.
     */
    public static function listarTodos()
    {
        $db = Database::getInstance()->getConnection();
        // Hacemos un JOIN con la tabla roles para obtener el nombre del rol
        $query = 'SELECT u.id, u.nombre, u.email, u.activo, r.nombre_rol FROM usuarios u LEFT JOIN roles r ON u.rol_id = r.id ORDER BY u.nombre ASC';
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza un usuario existente en la base de datos.
     */
    public function actualizar()
    {

        $query = 'UPDATE ' . $this->table . ' SET nombre = :nombre, email = :email, rol_id = :rol_id, activo = :activo WHERE id = :id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':rol_id', $this->rol_id, PDO::PARAM_INT);
        $stmt->bindParam(':activo', $this->activo, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Busca un usuario por su ID.
     * Útil para mostrar el formulario de edición.
     */
    public static function findById($id)
    {
        $db = Database::getInstance()->getConnection();
        // Hacemos un JOIN con roles para obtener también el rol_id
        $query = 'SELECT u.id, u.nombre, u.email, u.activo, u.rol_id 
              FROM usuarios u 
              WHERE u.id = :id 
              LIMIT 1';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}