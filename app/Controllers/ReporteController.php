<?php

namespace App\Controllers;

use App\Models\Colaborador;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ReporteController
{
    /**
     * Muestra el reporte principal de colaboradores y sus sueldos.
     */
    public function colaboradores()
    {
        $filtros = [
            'busqueda' => isset($_GET['busqueda']) ? $_GET['busqueda'] : null,
            'sexo' => isset($_GET['sexo']) ? $_GET['sexo'] : null,
            'salario_min' => isset($_GET['salario_min']) ? $_GET['salario_min'] : null
        ];

        // Lógica de Paginación
        $registros_por_pagina = 10;
        $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $offset = ($pagina_actual - 1) * $registros_por_pagina;

        $datosPaginados = Colaborador::listarParaReporte($filtros, $registros_por_pagina, $offset);

        $datosReporte = $datosPaginados['resultados'];
        $total_registros = $datosPaginados['total'];
        $total_paginas = ceil($total_registros / $registros_por_pagina);

        require_once __DIR__ . '/../../views/reportes/colaboradores.php';
    }

    /**
     * Exporta el reporte de colaboradores a un archivo Excel.
     */
    public function exportarColaboradores()
    {
        $filtros = [
            'busqueda' => isset($_GET['busqueda']) ? $_GET['busqueda'] : null,
            'sexo' => isset($_GET['sexo']) ? $_GET['sexo'] : null,
            'salario_min' => isset($_GET['salario_min']) ? $_GET['salario_min'] : null
        ];
        $datosReporte = \App\Models\Colaborador::listarParaReporte($filtros)['resultados'];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Colaboradores');

        // Encabezados
        $sheet->setCellValue('A1', 'Identificación');
        $sheet->setCellValue('B1', 'Nombre Completo');
        $sheet->setCellValue('C1', 'Correo');
        $sheet->setCellValue('D1', 'Departamento');
        $sheet->setCellValue('E1', 'Ocupación');
        $sheet->setCellValue('F1', 'Sueldo Actual');

        // Llenar la hoja con datos
        $row = 2;
        foreach ($datosReporte as $fila) {
            $sheet->setCellValue('A' . $row, $fila['identificacion']);
            $sheet->setCellValue('B' . $row, $fila['primer_nombre'] . ' ' . $fila['primer_apellido']);
            $sheet->setCellValue('C' . $row, $fila['correo_personal']);
            // --- INICIO DE LA CORRECCIÓN ---
            // Usamos ?? para dar un valor por defecto si el campo no existe
            $sheet->setCellValue('D' . $row, isset($fila['departamento']) ? $fila['departamento'] : 'N/A');
            $sheet->setCellValue('E' . $row, isset($fila['ocupacion']) ? $fila['ocupacion'] : 'N/A');
            $sheet->setCellValue('F' . $row, isset($fila['sueldo']) ? $fila['sueldo'] : 0);
            // --- FIN DE LA CORRECCIÓN ---
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD);
            $row++;
        }

        // Limpiamos cualquier salida de PHP que se haya generado antes (como warnings)
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Cabeceras para forzar la descarga
        $fileName = 'Reporte_Colaboradores_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}