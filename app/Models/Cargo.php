<?php
namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Clase Cargo
 *
 * Modelo que representa un cargo laboral de un colaborador en el sistema.
 * Maneja las operaciones CRUD de los cargos y la firma digital.
 *
 * @package App\Models
 * @author Analía Solís <analia.solis@utp.ac.pa> - ACJ Development Team
 * @version 1.0
 */
class Cargo
{
    /**
     * @var PDO Conexión a la base de datos
     */
    private $conn;

    /**
     * @var string Nombre de la tabla en la base de datos
     */
    private $table = 'cargos';

    /**
     * @var int|null ID único del cargo
     */
    public $id;

    /**
     * @var int ID del colaborador al que pertenece el cargo
     */
    public $colaborador_id;

    /**
     * @var float Sueldo del cargo
     */
    public $sueldo;

    /**
     * @var int ID del departamento
     */
    public $departamento_id;

    /**
     * @var string Ocupación o puesto de trabajo
     */
    public $ocupacion;

    /**
     * @var string Tipo de contrato (permanente, temporal, etc.)
     */
    public $tipo_contrato;

    /**
     * @var string Fecha de contratación (YYYY-MM-DD)
     */
    public $fecha_contratacion;

    /**
     * @var bool Estado del cargo (activo/inactivo)
     */
    public $activo;

    /**
     * @var string Firma digital del cargo
     */
    public $firma_digital;

    /**
     * Constructor de la clase Cargo.
     * Inicializa la conexión a la base de datos.
     */
    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Crea un nuevo cargo para un colaborador.
     * Desactiva cargos anteriores y genera firma digital.
     *
     * @return bool True si se creó exitosamente, false en caso contrario
     */
    public function crear()
    {
        // Antes de crear un nuevo cargo, desactivamos todos los cargos anteriores de este colaborador.
        $this->desactivarCargosAnteriores();
        $sueldo_formateado = number_format($this->sueldo, 2, '.', '');
        $datosParaFirmar = $this->colaborador_id . $sueldo_formateado . $this->departamento_id . $this->ocupacion . $this->fecha_contratacion;
        $this->firma_digital = $this->generarFirma($datosParaFirmar);

        // Actualizamos la consulta para usar departamento_id
        $query = 'INSERT INTO ' . $this->table . ' (colaborador_id, sueldo, departamento_id, ocupacion, tipo_contrato, fecha_contratacion, activo, firma_digital) VALUES (:colaborador_id, :sueldo, :departamento_id, :ocupacion, :tipo_contrato, :fecha_contratacion, TRUE, :firma_digital)';

        $stmt = $this->conn->prepare($query);

        // Sanitizar y vincular datos
        $stmt->bindParam(':colaborador_id', $this->colaborador_id, PDO::PARAM_INT);
        $stmt->bindParam(':sueldo', $this->sueldo);
        $stmt->bindParam(':departamento_id', $this->departamento_id, PDO::PARAM_INT); // <-- CAMBIO CLAVE
        $stmt->bindParam(':ocupacion', $this->ocupacion);
        $stmt->bindParam(':tipo_contrato', $this->tipo_contrato);
        $stmt->bindParam(':fecha_contratacion', $this->fecha_contratacion);
        $stmt->bindParam(':firma_digital', $this->firma_digital);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Desactiva todos los cargos existentes para un colaborador específico.
     *
     * @return void
     */
    private function desactivarCargosAnteriores()
    {
        $query = 'UPDATE ' . $this->table . ' SET activo = FALSE WHERE colaborador_id = :colaborador_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':colaborador_id', $this->colaborador_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Genera una firma digital para una cadena de datos usando la clave privada.
     *
     * @param string $datos Cadena de datos a firmar
     * @return string Firma digital en formato Base64
     * @throws Exception Si no se puede cargar la clave privada
     */
    private function generarFirma($datos)
    {
        // Leemos la clave privada desde el archivo.
        $privateKeyPath = __DIR__ . '/../../keys/private_key.pem';
        $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath));

        if (!$privateKey) {
            // Manejar error: no se pudo leer la clave.
            die('No se pudo cargar la clave privada.');
        }

        // Generamos la firma.
        openssl_sign($datos, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        // Liberamos la clave de la memoria.
        openssl_free_key($privateKey);

        // Devolvemos la firma en formato Base64 para guardarla fácilmente en la BD.
        return base64_encode($signature);
    }

    /**
     * Lista todos los cargos de un colaborador específico.
     *
     * @param int $colaborador_id ID del colaborador
     * @return array Array con los cargos del colaborador incluyendo datos del departamento
     */
    public static function listarPorColaborador($colaborador_id)
    {
        $db = Database::getInstance()->getConnection();
        $query = 'SELECT 
            ca.*, 
            d.nombre_departamento as departamento_nombre 
        FROM 
            cargos ca
        LEFT JOIN 
            departamentos d ON ca.departamento_id = d.id_departamento
        WHERE 
            ca.colaborador_id = :colaborador_id 
        ORDER BY 
            ca.fecha_contratacion DESC';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':colaborador_id', $colaborador_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica la integridad de los datos de un cargo usando su firma digital.
     *
     * @param array $cargo Array asociativo con los datos del cargo
     * @return bool True si la firma es válida, false si no lo es o si falta algún dato
     * @throws Exception Si no se puede cargar la clave pública
     */
    public static function verificarIntegridad($cargo)
    {
        if (empty($cargo['firma_digital'])) {
            return false;
        }
        $sueldo_formateado = number_format($cargo['sueldo'], 2, '.', '');
        // 1. Reconstruimos la cadena de datos original, en el MISMO ORDEN que al firmar.
        $datosOriginales = $cargo['colaborador_id'] . $sueldo_formateado . $cargo['departamento_id'] . $cargo['ocupacion'] . $cargo['fecha_contratacion'];

        // 2. Leemos la clave pública desde el archivo.
        $publicKeyPath = __DIR__ . '/../../keys/public_key.pem';
        $publicKey = openssl_pkey_get_public(file_get_contents($publicKeyPath));

        if (!$publicKey) {
            // Manejar error: no se pudo leer la clave.
            die('No se pudo cargar la clave pública.');
        }

        // 3. Decodificamos la firma que está en formato Base64.
        $firma = base64_decode($cargo['firma_digital']);

        // 4. Verificamos la firma con los datos y la clave pública.
        // openssl_verify() devuelve 1 si es válido, 0 si no, -1 si hay un error.
        $esValido = openssl_verify($datosOriginales, $firma, $publicKey, OPENSSL_ALGO_SHA256);

        // Liberamos la clave de la memoria.
        openssl_free_key($publicKey);

        return $esValido === 1;
    }

    /**
     * Encuentra la primera fecha de contratación de un colaborador.
     *
     * @param int $colaborador_id ID del colaborador
     * @return string|null Fecha de primera contratación (YYYY-MM-DD) o null si no se encuentra
     */
    public static function obtenerPrimeraContratacion($colaborador_id)
    {
        $db = Database::getInstance()->getConnection();
        // Busca la fecha de contratación más antigua (el primer registro)
        $query = 'SELECT fecha_contratacion FROM cargos WHERE colaborador_id = :colaborador_id ORDER BY fecha_contratacion ASC LIMIT 1';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':colaborador_id', $colaborador_id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['fecha_contratacion'] : null;
    }
}