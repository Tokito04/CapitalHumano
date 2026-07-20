<?php

namespace App\Controllers\Api;

use App\Models\Colaborador;

/**
 * Clase ApiController
 *
 * Controlador que proporciona endpoints de API REST para consultas externas
 * del sistema de Capital Humano. Incluye autenticación por API Key.
 *
 * @package App\Controllers\Api
 * @author Joseph Guerrero <joseph.guerrero2@utp.ac.pa> - ACJ Development Team
 * @version 1.0
 */
class ApiController
{
    /**
     * Verifica que la petición esté autorizada: o bien hay una sesión de
     * usuario activa (uso interno, p.ej. el dashboard), o bien se envía una
     * API Key válida (consumo externo, p.ej. Contraloría). La clave se lee
     * de una variable de entorno, nunca queda hardcodeada en el código
     * fuente, y se compara en tiempo constante con hash_equals().
     *
     * @return bool
     */
    private function autorizado(): bool
    {
        if (isset($_SESSION['user_id'])) {
            return true;
        }

        $apiKeyEsperada = $_ENV['API_KEY_CONTRALORIA'] ?? '';
        $apiKeyRecibida = $_GET['apikey'] ?? '';

        return $apiKeyEsperada !== '' && hash_equals($apiKeyEsperada, $apiKeyRecibida);
    }

    /**
     * Devuelve las estadísticas de colaboradores por sexo.
     * Requiere sesión activa o autenticación mediante API Key.
     *
     * @return void Devuelve respuesta JSON con estadísticas por sexo
     */
    public function estadisticasSexo()
    {
        if (!$this->autorizado()) {
            header('Content-Type: application/json');
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'API Key no válida o no proporcionada.']);
            exit();
        }

        $datos = Colaborador::contarPorSexo();

        // Formatear los datos para una respuesta más clara
        $respuesta = [
            'fecha_consulta' => date('Y-m-d H:i:s'),
            'fuente' => 'Sistema de Capital Humano',
            'datos' => []
        ];

        foreach ($datos as $fila) {
            $sexo = ($fila['sexo'] === 'M') ? 'Masculino' : 'Femenino';
            $respuesta['datos'][$sexo] = (int)$fila['total'];
        }

        // Establecer la cabecera para indicar que la respuesta es JSON
        header('Content-Type: application/json');
        echo json_encode($respuesta, JSON_PRETTY_PRINT);
        exit();
    }

    /**
     * Cuenta el total de colaboradores activos agrupados por rangos de edad.
     * Método estático para uso interno.
     *
     * @return array Array con el conteo por rangos de edad
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
     *
     * @return array Array con el conteo por dirección
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

    /**
     * Devuelve un conjunto completo de estadísticas para los gráficos del dashboard.
     * Requiere sesión activa o autenticación mediante API Key.
     *
     * @return void Devuelve respuesta JSON con todas las estadísticas del sistema
     */
    public function estadisticasGenerales()
    {
        if (!$this->autorizado()) {
            header('Content-Type: application/json');
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'API Key no válida o no proporcionada.']);
            exit();
        }

        // Obtenemos todos los datos estadísticos de nuestros modelos
        $stats_sexo = Colaborador::contarPorSexo();
        $stats_edad = Colaborador::contarPorRangoEdad();
        $stats_direccion = Colaborador::contarPorDireccion();
        $stats_departamento = Colaborador::contarPorDepartamento(); // Asumiendo que existe este método

        // Formateamos la respuesta final en un objeto JSON ordenado
        $respuesta = [
            'por_sexo' => [],
            'por_edad' => [
                'labels' => [],
                'data' => []
            ],
            'por_direccion' => [
                'labels' => [],
                'data' => []
            ],
            'por_departamento' => [
                'data' => []
            ]
        ];

        // Procesar datos por sexo
        foreach ($stats_sexo as $fila) {
            $sexo = ($fila['sexo'] === 'M') ? 'Masculino' : 'Femenino';
            $respuesta['por_sexo'][$sexo] = (int)$fila['total'];
        }

        // Procesar datos por rango de edad
        foreach ($stats_edad as $fila) {
            $respuesta['por_edad']['labels'][] = $fila['rango_edad'];
            $respuesta['por_edad']['data'][] = (int)$fila['total'];
        }

        // Procesar datos por dirección
        foreach ($stats_direccion as $fila) {
            $respuesta['por_direccion']['labels'][] = $fila['direccion'];
            $respuesta['por_direccion']['data'][] = (int)$fila['total'];
        }

        // Procesar datos por departamento
        foreach ($stats_departamento as $fila) {
            $respuesta['por_departamento']['data'][] = (int)$fila['total'];
        }

        header('Content-Type: application/json');
        echo json_encode($respuesta, JSON_PRETTY_PRINT);
        exit();
    }
}