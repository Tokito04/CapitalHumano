<?php

namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

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
    /**
     * Obtiene los colaboradores de forma paginada.
     * También devuelve el total de registros activos.
     */
    public static function listarTodos($limit = 10, $offset = 0)
    {
        $db = Database::getInstance()->getConnection();

        // Consulta para obtener los datos paginados
        $query_datos = 'SELECT * FROM colaboradores WHERE activo = TRUE ORDER BY primer_apellido ASC LIMIT :limit OFFSET :offset';
        $stmt_datos = $db->prepare($query_datos);
        $stmt_datos->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt_datos->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt_datos->execute();
        $resultados = $stmt_datos->fetchAll(PDO::FETCH_ASSOC);

        // Consulta para contar el total de registros activos
        $query_total = 'SELECT COUNT(id) FROM colaboradores WHERE activo = TRUE';
        $stmt_total = $db->prepare($query_total);
        $stmt_total->execute();
        $total_filas = $stmt_total->fetchColumn();

        return ['resultados' => $resultados, 'total' => $total_filas];
    }

    /**
     * Crea un nuevo colaborador en la base de datos.
     */
    public function crear()
    {
        $query = 'INSERT INTO ' . $this->table . ' (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, sexo, identificacion, fecha_nacimiento, foto_perfil, correo_personal, telefono, celular, direccion) VALUES (:primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, :sexo, :identificacion, :fecha_nacimiento, :foto_perfil, :correo_personal, :telefono, :celular, :direccion)';

        $stmt = $this->conn->prepare($query);


        // Vincular datos
        $stmt->bindParam(':primer_nombre', $this->primer_nombre);
        $stmt->bindParam(':segundo_nombre', $this->segundo_nombre);
        $stmt->bindParam(':primer_apellido', $this->primer_apellido);
        $stmt->bindParam(':segundo_apellido', $this->segundo_apellido);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':identificacion', $this->identificacion);
        $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);
        $stmt->bindParam(':foto_perfil', $this->foto_perfil);
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
        try {
            // 1. Revisa que la consulta SQL sea EXACTAMENTE igual a los nombres de tus columnas.
            $query = 'UPDATE ' . $this->table . ' SET 
                    primer_nombre = :primer_nombre, 
                    segundo_nombre = :segundo_nombre, 
                    primer_apellido = :primer_apellido, 
                    segundo_apellido = :segundo_apellido, 
                    sexo = :sexo, 
                    identificacion = :identificacion, 
                    fecha_nacimiento = :fecha_nacimiento, 
                    foto_perfil = :foto_perfil,
                    correo_personal = :correo_personal, 
                    telefono = :telefono, 
                    celular = :celular, 
                    direccion = :direccion 
                  WHERE id = :id';

            $stmt = $this->conn->prepare($query);

            // 2. Vincula todos los parámetros.
            $stmt->bindParam(':primer_nombre', $this->primer_nombre);
            $stmt->bindParam(':segundo_nombre', $this->segundo_nombre);
            $stmt->bindParam(':primer_apellido', $this->primer_apellido);
            $stmt->bindParam(':segundo_apellido', $this->segundo_apellido);
            $stmt->bindParam(':sexo', $this->sexo);
            $stmt->bindParam(':identificacion', $this->identificacion);
            $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);
            $stmt->bindParam(':foto_perfil', $this->foto_perfil);
            $stmt->bindParam(':correo_personal', $this->correo_personal);
            $stmt->bindParam(':telefono', $this->telefono);
            $stmt->bindParam(':celular', $this->celular);
            $stmt->bindParam(':direccion', $this->direccion);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

            // 3. Intenta ejecutar la consulta.
            $stmt->execute();

            // 4. Comprueba si la consulta realmente afectó a alguna fila.
            if ($stmt->rowCount() > 0) {
                return true; // ¡Éxito!
            } else {
                // La consulta se ejecutó sin errores, pero no actualizó ninguna fila.
                // Esto usualmente significa que el ID no se encontró.
                die("Depuración: La consulta se ejecutó pero no se actualizó ninguna fila. ¿Es correcto el ID: " . $this->id . "?");
            }

        } catch (PDOException $e) {
            // Si hay cualquier error en el prepare() o execute(), se captura aquí.
            die("Error de base de datos al actualizar: " . $e->getMessage());
        }
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

    /**
     * Obtiene una lista de todos los colaboradores con los datos de su cargo activo.
     * Ideal para reportes.
     */
    public static function listarParaReporte($filtros = [], $limit = 10, $offset = 0)
    {
        $db = Database::getInstance()->getConnection();

        $query_base = '
        FROM 
            colaboradores c
        LEFT JOIN 
            cargos ca ON c.id = ca.colaborador_id AND ca.activo = TRUE
        WHERE
            c.activo = TRUE';

        $params = [];
        $query_filtros = '';

        // Construir la parte de los filtros
        if (!empty($filtros['busqueda'])) {
            $query_filtros .= ' AND (c.primer_nombre ILIKE :busqueda OR c.primer_apellido ILIKE :busqueda)';
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        if (!empty($filtros['sexo'])) {
            $query_filtros .= ' AND c.sexo = :sexo';
            $params[':sexo'] = $filtros['sexo'];
        }
        if (!empty($filtros['salario_min'])) {
            $query_filtros .= ' AND ca.sueldo >= :salario_min';
            $params[':salario_min'] = $filtros['salario_min'];
        }

        // Consulta para obtener los datos paginados
        $query_datos = 'SELECT c.id, c.identificacion, c.primer_nombre, c.primer_apellido, c.correo_personal, ca.sueldo, ca.departamento, ca.ocupacion ' . $query_base . $query_filtros . ' ORDER BY c.primer_apellido ASC LIMIT :limit OFFSET :offset';

        $stmt_datos = $db->prepare($query_datos);
        // Añadir parámetros de paginación
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        // Vincular todos los parámetros
        foreach ($params as $key => &$val) {
            if(is_int($val)) {
                $stmt_datos->bindParam($key, $val, PDO::PARAM_INT);
            } else {
                $stmt_datos->bindParam($key, $val);
            }
        }

        $stmt_datos->execute();
        $resultados = $stmt_datos->fetchAll(PDO::FETCH_ASSOC);

        // Consulta para contar el total de resultados filtrados
        $query_total = 'SELECT COUNT(c.id) ' . $query_base . $query_filtros;
        $stmt_total = $db->prepare($query_total);
        // Volvemos a ejecutar solo con los filtros, sin paginación
        unset($params[':limit'], $params[':offset']);
        $stmt_total->execute($params);
        $total_filas = $stmt_total->fetchColumn();

        return ['resultados' => $resultados, 'total' => $total_filas];
    }
}