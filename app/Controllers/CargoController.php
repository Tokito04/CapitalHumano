<?php

namespace App\Controllers;

use App\Models\Cargo;
use App\Models\Departamento;
use App\Models\Colaborador;
use App\Utils\Validator;

class CargoController
{
    /**
     * Muestra el formulario para añadir un nuevo cargo a un colaborador.
     */
    public function showCreateForm()
    {
        $colaborador_id = $_GET['colaborador_id'];
        // Obtenemos los datos del colaborador para mostrar su nombre en la vista
        $colaborador = Colaborador::findById($colaborador_id);
        $departamentos = Departamento::listarTodos(); // Obtenemos la lista

        require_once __DIR__ . '/../../views/cargos/create.php';
    }

    /**
     * Almacena un nuevo cargo en la base de datos.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Aquí iría la validación de los datos del formulario...

            $cargo = new Cargo();
            $cargo->colaborador_id = $_POST['colaborador_id'];
            $cargo->sueldo = $_POST['sueldo'];
            $cargo->departamento_id = Validator::sanitizeString($_POST['departamento_id']);
            $cargo->ocupacion = Validator::sanitizeString($_POST['ocupacion']);
            $cargo->tipo_contrato = $_POST['tipo_contrato'];
            $cargo->fecha_contratacion = $_POST['fecha_contratacion'];

            if ($cargo->crear()) {
                // Redirigir a la página de detalles del colaborador
                header('Location: ' . BASE_PATH . '/colaboradores/editar?id=' . $cargo->colaborador_id);
                exit();
            }
        }
    }
}
