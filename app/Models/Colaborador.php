<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Colaborador
{
    private $conn;
    private $table = 'colaboradores';

// Propiedades del Colaborador basadas en la rúbrica
    public $id;
    public $primer_nombre;
    public $segundo_nombre;
    public $primer_apellido;
    public $segundo_apellido;
    public $sexo;
    public $identificacion;
    public $fecha_nacimiento;
    public $foto_perfil;
    public $correo_personal;
    public $telefono;
    public $celular;
    public $direccion;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene todos los colaboradores de la base de datos.
     */
    public static function listarTodos()
    {
        $db = Database::getInstance()->getConnection();
        $query = 'SELECT * FROM colaboradores WHERE activo = TRUE ORDER BY primer_apellido ASC';
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un nuevo colaborador en la base de datos.
     */
    public function crear()
    {
        $query = 'INSERT INTO ' . $this->table . ' (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, sexo, identificacion, fecha_nacimiento, correo_personal, telefono, celular, direccion) VALUES (:primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, :sexo, :identificacion, :fecha_nacimiento, :correo_personal, :telefono, :celular, :direccion)';

        $stmt = $this->conn->prepare($query);

        // Limpiar datos (Sanitización básica)
        $this->primer_nombre = htmlspecialchars(strip_tags($this->primer_nombre));
        // ... (se haría lo mismo para las otras propiedades) ...

        // Vincular datos
        $stmt->bindParam(':primer_nombre', $this->primer_nombre);
        $stmt->bindParam(':segundo_nombre', $this->segundo_nombre);
        $stmt->bindParam(':primer_apellido', $this->primer_apellido);
        $stmt->bindParam(':segundo_apellido', $this->segundo_apellido);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':identificacion', $this->identificacion);
        $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);
        $stmt->bindParam(':correo_personal', $this->correo_personal);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':celular', $this->celular);
        $stmt->bindParam(':direccion', $this->direccion);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Actualiza un colaborador existente en la base de datos.
     */
    public function actualizar()
    {
        $query = 'UPDATE ' . $this->table . ' SET primer_nombre = :primer_nombre, segundo_nombre = :segundo_nombre, primer_apellido = :primer_apellido, segundo_apellido = :segundo_apellido, sexo = :sexo, identificacion = :identificacion, fecha_nacimiento = :fecha_nacimiento, correo_personal = :correo_personal, telefono = :telefono, celular = :celular, direccion = :direccion WHERE id = :id';

        $stmt = $this->conn->prepare($query);

        // Limpiar y vincular datos
        $stmt->bindParam(':primer_nombre', $this->primer_nombre);
        $stmt->bindParam(':segundo_nombre', $this->segundo_nombre);
        $stmt->bindParam(':primer_apellido', $this->primer_apellido);
        $stmt->bindParam(':segundo_apellido', $this->segundo_apellido);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':identificacion', $this->identificacion);
        $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);
        $stmt->bindParam(':correo_personal', $this->correo_personal);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':celular', $this->celular);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Busca un colaborador por su ID.
     */
    public static function findById($id)
    {
        $db = Database::getInstance()->getConnection();
        $query = 'SELECT * FROM colaboradores WHERE id = :id LIMIT 1';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cambia el estado de un colaborador (activo/inactivo).
     */
    public static function cambiarEstado($id, $nuevo_estado)
    {
        $db = Database::getInstance()->getConnection();
        $query = 'UPDATE colaboradores SET activo = :activo WHERE id = :id';
        $stmt = $db->prepare($query);

        $stmt->bindParam(':activo', $nuevo_estado, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}