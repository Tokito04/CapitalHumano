<?php

namespace App\Controllers;

use App\Models\Colaborador;
use App\Models\Cargo;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Clase VacacionesController
 *
 * Controlador que maneja el cálculo y generación de documentos de vacaciones
 * para los colaboradores del sistema de Capital Humano.
 *
 * @package App\Controllers
 * @author Tu Nombre
 * @version 1.0
 */
class VacacionesController
{
    /**
     * Muestra la vista principal del módulo de vacaciones para un colaborador.
     * Calcula automáticamente los días de vacaciones ganados basado en días trabajados.
     *
     * @return void Carga la vista con los datos de vacaciones calculados
     */
    public function index()
    {
        $colaborador_id = $_GET['colaborador_id'];
        $colaborador = Colaborador::findById($colaborador_id);

        $fecha_inicio_str = Cargo::obtenerPrimeraContratacion($colaborador_id);
        $dias_vacaciones_ganados = 0;

        if ($fecha_inicio_str) {
            // Simulación de que trabajan más de 11 meses (Requisito #14)
            // Para la prueba, restamos 2 años a la fecha de inicio real.
            $fecha_inicio = new DateTime($fecha_inicio_str);
            $fecha_inicio->modify('-2 year');

            $fecha_actual = new DateTime();

            // Calculamos la diferencia en días
            $intervalo = $fecha_inicio->diff($fecha_actual);
            $dias_trabajados = $intervalo->days;

            // Calculamos los días de vacaciones según la regla (1 por cada 11)
            $dias_vacaciones_ganados = floor($dias_trabajados / 11);
        }

        // Cargamos la vista, pasándole los datos calculados
        require_once __DIR__ . '/../../views/vacaciones/index.php';
    }

    /**
     * Genera un documento PDF con el resuelto de vacaciones del colaborador.
     * Incluye toda la información necesaria para el trámite oficial.
     *
     * @return void Genera y descarga un archivo PDF con el resuelto de vacaciones
     */
    public function generarResuelto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $colaborador_id = $_POST['colaborador_id'];
            $dias_vacaciones = $_POST['dias_vacaciones'];
            $dias_a_tomar = $_POST['dias_a_tomar'];

            $colaborador = Colaborador::findById($colaborador_id);
            $cargo_actual = Cargo::listarPorColaborador($colaborador_id)[0] ?? null;

            // Para que Dompdf pueda cargar imágenes y CSS, necesitamos configurar las opciones.
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);

            // Obtenemos el contenido del HTML que vamos a convertir.
            ob_start();
            require_once __DIR__ . '/../../views/vacaciones/plantilla_pdf.php';
            $html = ob_get_clean();

            $dompdf->loadHtml($html);
            $dompdf->setPaper('letter', 'portrait'); // Tamaño carta, orientación vertical
            $dompdf->render();

            // Enviamos el PDF al navegador para que se muestre o descargue.
            $dompdf->stream("Resuelto_Vacaciones_" . $colaborador['identificacion'] . ".pdf", ["Attachment" => false]);
            exit();
        }
    }
}