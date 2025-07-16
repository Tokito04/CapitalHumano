<?php

namespace App\Controllers;

use App\Models\Colaborador;

class ColaboradorController
{
    /**
     * Muestra la lista de todos los colaboradores.
     */
    public function index()
    {
        $colaboradores = Colaborador::listarTodos();
        require_once __DIR__ . '/../../views/colaboradores/index.php';
    }

    /**
     * Muestra el formulario para crear un nuevo colaborador.
     */
    public function showCreateForm()
    {
        require_once __DIR__ . '/../../views/colaboradores/create.php';
    }

    /**
     * Muestra el formulario para editar un colaborador existente.
     */
    public function showEditForm()
    {
        // Obtenemos el ID del colaborador de la URL
        $id = $_GET['id'];
        $colaborador = Colaborador::findById($id);

        // Si el colaborador no existe, podríamos redirigir a un error 404
        if (!$colaborador) {
            // Manejar el error...
            exit('Colaborador no encontrado.');
        }

        // Cargamos la vista del formulario de edición, pasándole los datos
        require_once __DIR__ . '/../../views/colaboradores/edit.php';
    }

    /**
     * Procesa la actualización de un colaborador.
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $colaborador = new Colaborador();

            // Asignar todos los valores desde el POST
            $colaborador->id = $_POST['id'];
            $colaborador->primer_nombre = $_POST['primer_nombre'];
            $colaborador->segundo_nombre = $_POST['segundo_nombre'];
            $colaborador->primer_apellido = $_POST['primer_apellido'];
            $colaborador->segundo_apellido = $_POST['segundo_apellido'];
            $colaborador->sexo = $_POST['sexo'];
            $colaborador->identificacion = $_POST['identificacion'];
            $colaborador->fecha_nacimiento = $_POST['fecha_nacimiento'];
            $colaborador->correo_personal = $_POST['correo_personal'];
            $colaborador->telefono = $_POST['telefono'];
            $colaborador->celular = $_POST['celular'];
            $colaborador->direccion = $_POST['direccion'];

            if ($colaborador->actualizar()) {
                header('Location: ' . BASE_PATH . '/colaboradores');
                exit();
            }
        }
    }

    /**
     * Almacena un nuevo colaborador en la base de datos.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $colaborador = new Colaborador();
            $colaborador->primer_nombre = $_POST['primer_nombre'];
            $colaborador->segundo_nombre = $_POST['segundo_nombre'];
            $colaborador->primer_apellido = $_POST['primer_apellido'];
            $colaborador->segundo_apellido = $_POST['segundo_apellido'];
            $colaborador->sexo = $_POST['sexo'];
            $colaborador->identificacion = $_POST['identificacion'];
            $colaborador->fecha_nacimiento = $_POST['fecha_nacimiento'];
            $colaborador->correo_personal = $_POST['correo_personal'];
            $colaborador->telefono = $_POST['telefono'];
            $colaborador->celular = $_POST['celular'];
            $colaborador->direccion = $_POST['direccion'];

            if ($colaborador->crear()) {
                header('Location: ' . BASE_PATH . '/colaboradores');
                exit();
            }
        }
    }
    /**
     * Cambia el estado de un colaborador de activo a inactivo y viceversa.
     */
    public function toggleStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $estado_actual = (bool)$_POST['estado_actual'];

            // Invierte el estado
            $nuevo_estado = !$estado_actual;

            Colaborador::cambiarEstado($id, $nuevo_estado);

            header('Location: ' . BASE_PATH . '/colaboradores');
            exit();
        }
    }
}