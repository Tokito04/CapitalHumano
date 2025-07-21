<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Departamento
{
    private $conn;
    private $table = 'departamentos';

    public $id;
    public $nombre;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene todos los departamentos de la base de datos.
     */
    public static function listarTodos()
    {
        $db = Database::getInstance()->getConnection();
        $query = 'SELECT * FROM departamentos ORDER BY nombre_departamento ASC';
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}