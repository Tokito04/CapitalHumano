<?php

namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Clase Vacacion
 *
 * Representa un registro de un resuelto de vacaciones generado.
 * @package App\Models
 * @author Joseph Guerrero <joseph.guerrero2@utp.ac.pa> ACJ Development Team
 * @version 1.0
 */
class Vacacion
{
    private $conn;
    private $table = 'vacaciones';

    public $id;
    public $colaborador_id;
    public $fecha_resuelto;
    public $dias_tomados;
    public $documento_pdf_url;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Crea un nuevo registro de vacaciones en la base de datos.
     *
     * @return bool True si la creación fue exitosa, false en caso contrario.
     */
    public function crear()
    {
        $query = 'INSERT INTO ' . $this->table . ' (colaborador_id, fecha_resuelto, dias_tomados, documento_pdf_url) VALUES (:colaborador_id, NOW(), :dias_tomados, :documento_pdf_url)';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':colaborador_id', $this->colaborador_id, PDO::PARAM_INT);
        $stmt->bindParam(':dias_tomados', $this->dias_tomados, PDO::PARAM_INT);
        $stmt->bindParam(':documento_pdf_url', $this->documento_pdf_url);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
