<?php

namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Clase Departamento
 *
 * Modelo que representa un departamento en el sistema de Capital Humano.
 * Maneja las operaciones relacionadas con los departamentos organizacionales.
 *
 * @package App\Models
 * @author Tu Nombre
 * @version 1.0
 */
class Departamento
{
    /**
     * @var PDO Conexión a la base de datos
     */
    private $conn;

    /**
     * @var string Nombre de la tabla en la base de datos
     */
    private $table = 'departamentos';

    /**
     * @var int|null ID único del departamento
     */
    public $id;

    /**
     * @var string Nombre del departamento
     */
    public $nombre;

    /**
     * Constructor de la clase Departamento.
     * Inicializa la conexión a la base de datos.
     */
    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene todos los departamentos de la base de datos.
     * Ordena los resultados por nombre de departamento.
     *
     * @return array Array con todos los departamentos ordenados alfabéticamente
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