<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Cargo
{
    private $conn;
    private $table = 'cargos';

    // Propiedades del objeto
    public $id;
    public $colaborador_id;
    public $sueldo;
    public $departamento;
    public $ocupacion;
    public $tipo_contrato;
    public $fecha_contratacion;
    public $activo;
    public $firma_digital;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Crea un nuevo cargo para un colaborador.
     */
    public function crear()
    {
        // Antes de crear un nuevo cargo, desactivamos todos los cargos anteriores de este colaborador.
        $this->desactivarCargosAnteriores();
        $sueldo_formateado = number_format($this->sueldo, 2, '.', '');
        $datosParaFirmar = $this->colaborador_id . $sueldo_formateado . $this->departamento . $this->ocupacion . $this->fecha_contratacion;

        // 2. Firmamos los datos con nuestra clave privada.
        $this->firma_digital = $this->generarFirma($datosParaFirmar);
        // 3. Modificamos la consulta para incluir la firma.
        $query = 'INSERT INTO ' . $this->table . ' (colaborador_id, sueldo, departamento, ocupacion, tipo_contrato, fecha_contratacion, activo, firma_digital) VALUES (:colaborador_id, :sueldo, :departamento, :ocupacion, :tipo_contrato, :fecha_contratacion, TRUE, :firma_digital)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':colaborador_id', $this->colaborador_id, PDO::PARAM_INT);
        $stmt->bindParam(':sueldo', $this->sueldo);
        $stmt->bindParam(':departamento', $this->departamento);
        $stmt->bindParam(':ocupacion', $this->ocupacion);
        $stmt->bindParam(':tipo_contrato', $this->tipo_contrato);
        // ... (los bindParam para los otros campos no cambian) ...
        $stmt->bindParam(':fecha_contratacion', $this->fecha_contratacion);

        // 4. Vinculamos la nueva firma digital.
        $stmt->bindParam(':firma_digital', $this->firma_digital);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Desactiva todos los cargos existentes para un colaborador específico.
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
     */
    public static function listarPorColaborador($colaborador_id)
    {
        $db = Database::getInstance()->getConnection();
        $query = 'SELECT * FROM cargos WHERE colaborador_id = :colaborador_id ORDER BY fecha_contratacion DESC';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':colaborador_id', $colaborador_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica la integridad de los datos de un cargo usando su firma digital.
     *
     * @param array $cargo Un array asociativo con los datos del cargo.
     * @return bool True si la firma es válida, false si no lo es o si falta algún dato.
     */
    public static function verificarIntegridad($cargo)
    {
        if (empty($cargo['firma_digital'])) {
            return false;
        }

        // 1. Reconstruimos la cadena de datos original, en el MISMO ORDEN que al firmar.
        $datosOriginales = $cargo['colaborador_id'] . $cargo['sueldo'] . $cargo['departamento'] . $cargo['ocupacion'] . $cargo['fecha_contratacion'];

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
}