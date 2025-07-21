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
    public $estatus;
    public $historial_academico_pdf;

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
        $query = 'INSERT INTO ' . $this->table . ' (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, sexo, identificacion, fecha_nacimiento, foto_perfil, correo_personal, telefono, celular, direccion, historial_academico_pdf) VALUES (:primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, :sexo, :identificacion, :fecha_nacimiento, :foto_perfil, :correo_personal, :telefono, :celular, :direccion, :historial_academico_pdf)';

        $stmt = $this->conn->prepare($query);


        // Vincular datos
        $stmt->bindParam(':primer_nombre', $this->primer_nombre);
        $stmt->bindParam(':segundo_nombre', $this->segundo_nombre);
        $stmt->bindParam(':primer_apellido', $this->primer_apellido);
        $stmt->bindParam(':segundo_apellido', $this->segundo_apellido);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':historial_academico_pdf', $this->historial_academico_pdf);
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
                    direccion = :direccion,
                    estatus = :estatus
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
            $stmt->bindParam(':estatus', $this->estatus);
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

        // 1. Definimos la base de la consulta con los JOINs
        $query_base = '
        FROM 
            colaboradores c
        LEFT JOIN 
            cargos ca ON c.id = ca.colaborador_id AND ca.activo = TRUE
        LEFT JOIN
            departamentos d ON ca.departamento_id = d.id_departamento
        WHERE
            c.activo = TRUE';

        $params = [];
        $query_filtros = '';

        // 2. Construimos la parte de los filtros dinámicamente
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

        // --- SECCIÓN CORREGIDA ---

        // 3. Consulta para obtener los datos paginados
        $query_datos = 'SELECT c.id, c.identificacion, c.primer_nombre, c.primer_apellido, c.correo_personal, ca.sueldo, d.nombre_departamento as departamento, ca.ocupacion ' . $query_base . $query_filtros . ' ORDER BY ca.sueldo DESC LIMIT :limit OFFSET :offset';

        $stmt_datos = $db->prepare($query_datos);

        // Añadir los parámetros de paginación a la lista de parámetros
        $params_paginados = $params; // Copiamos los filtros
        $params_paginados[':limit'] = $limit;
        $params_paginados[':offset'] = $offset;

        // Vincular todos los parámetros
        foreach ($params_paginados as $key => &$val) {
            if(is_int($val)) {
                $stmt_datos->bindParam($key, $val, \PDO::PARAM_INT);
            } else {
                $stmt_datos->bindParam($key, $val);
            }
        }

        $stmt_datos->execute();
        $resultados = $stmt_datos->fetchAll(\PDO::FETCH_ASSOC);

        // 4. Consulta para contar el total de filas (con los mismos filtros)
        $query_total = 'SELECT COUNT(c.id) ' . $query_base . $query_filtros;
        $stmt_total = $db->prepare($query_total);
        $stmt_total->execute($params); // Usamos solo los parámetros de filtro
        $total_filas = $stmt_total->fetchColumn();

        // 5. Devolvemos el arreglo completo
        return ['resultados' => $resultados, 'total' => $total_filas];
    }

    /**
     * Cuenta el total de colaboradores activos agrupados por sexo.
     */
    public static function contarPorSexo()
    {
        $db = Database::getInstance()->getConnection();
        $query = "SELECT sexo, COUNT(id) as total FROM colaboradores WHERE activo = TRUE GROUP BY sexo";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta el total de colaboradores activos agrupados por rangos de edad.
     */
    public static function contarPorRangoEdad()
    {
        $db = Database::getInstance()->getConnection();
        // Esta consulta calcula la edad de cada colaborador y la agrupa en rangos.
        $query = "
        SELECT 
            CASE
                WHEN date_part('year', age(fecha_nacimiento)) BETWEEN 18 AND 25 THEN '18-25'
                WHEN date_part('year', age(fecha_nacimiento)) BETWEEN 26 AND 35 THEN '26-35'
                WHEN date_part('year', age(fecha_nacimiento)) BETWEEN 36 AND 45 THEN '36-45'
                WHEN date_part('year', age(fecha_nacimiento)) > 45 THEN 'Más de 45'
                ELSE 'Menor de 18'
            END as rango_edad,
            COUNT(*) as total
        FROM colaboradores
        WHERE activo = TRUE
        GROUP BY rango_edad
        ORDER BY rango_edad;
    ";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta el total de colaboradores activos agrupados por dirección.
     * Limita los resultados a las 10 direcciones más comunes para claridad.
     */
    public static function contarPorDireccion()
    {
        $db = Database::getInstance()->getConnection();
        $query = "
        SELECT direccion, COUNT(id) as total 
        FROM colaboradores 
        WHERE activo = TRUE AND direccion IS NOT NULL AND direccion != ''
        GROUP BY direccion 
        ORDER BY total DESC 
        LIMIT 10;
    ";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function contarPorDepartamento()
    {
        $db = Database::getInstance()->getConnection();
        $query = "
        SELECT count(nombre_departamento) as total from departamentos";

        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}