<?php

namespace App\Controllers;

use App\Models\Colaborador;
use App\Models\Cargo;
use App\Utils\Validator;

/**
 * Clase ColaboradorController
 *
 * Controlador que maneja todas las operaciones relacionadas con los colaboradores.
 * Incluye funcionalidades de CRUD, validaciones y manejo de archivos.
 *
 * @package App\Controllers
 * @author Carlos Echevers <carlos.echevers@utp.ac.pa> ACJ Develpment Team
 * @version 1.0
 */
class ColaboradorController
{
    /**
     * Muestra la lista de todos los colaboradores con paginación.
     *
     * @return void Carga la vista con la lista paginada de colaboradores
     */
    public function index()
    {
        // Lógica de Paginación
        $registros_por_pagina = 10;
        $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $offset = ($pagina_actual - 1) * $registros_por_pagina;

        // Obtenemos los datos paginados desde el modelo
        $datosPaginados = Colaborador::listarTodos($registros_por_pagina, $offset);

        $colaboradores = $datosPaginados['resultados'];
        $total_registros = $datosPaginados['total'];
        $total_paginas = ceil($total_registros / $registros_por_pagina);

        require_once __DIR__ . '/../../views/colaboradores/index.php';
    }

    /**
     * Muestra el formulario para crear un nuevo colaborador.
     *
     * @return void Carga la vista del formulario de creación
     */
    public function showCreateForm()
    {
        require_once __DIR__ . '/../../views/colaboradores/create.php';
    }

    /**
     * Muestra el formulario para editar un colaborador existente.
     *
     * @return void Carga la vista del formulario de edición con los datos del colaborador
     * @throws Exception Si el colaborador no se encuentra
     */
    public function showEditForm()
    {
        // Obtenemos el ID del colaborador de la URL
        $id = $_GET['id'];
        $colaborador = Colaborador::findById($id);
        $cargos = Cargo::listarPorColaborador($id);
        // Si el colaborador no existe, podríamos redirigir a un error 404
        if (!$colaborador) {
            // Manejar el error...
            exit('Colaborador no encontrado.');
        }

        // Cargamos la vista del formulario de edición, pasándole los datos
        require_once __DIR__ . '/../../views/colaboradores/edit.php';
    }

    /**
     * Procesa la actualización de un colaborador existente.
     * Incluye validación de datos y manejo de archivos (foto y PDF).
     *
     * @return void Redirige según el resultado de la operación
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];

            // 1. Validar Datos
            if (!Validator::validateRequired($_POST['primer_nombre'])) $errors[] = "El primer nombre es obligatorio.";
            if (!Validator::validateRequired($_POST['primer_apellido'])) $errors[] = "El primer apellido es obligatorio.";

            $identificacion_sanitizada = Validator::sanitizeAlphaNumeric($_POST['identificacion']);
            if (!Validator::validatePanamanianID($identificacion_sanitizada)) $errors[] = "El formato de la identificación no es válido.";

            // Añadir más validaciones si es necesario...

            // 2. Si hay errores, redirigir de vuelta
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ' . BASE_PATH . '/colaboradores/editar?id=' . $_POST['id']);
                exit();
            }

            // 3. Si no hay errores, crear y poblar el objeto Colaborador
            $colaborador = new Colaborador();

            // ¡¡ESTA ES LA PARTE MÁS IMPORTANTE!!
            $colaborador->id = $_POST['id']; // Asignamos el ID desde el POST

            $colaborador->primer_nombre = Validator::sanitizeString($_POST['primer_nombre']);
            $colaborador->segundo_nombre = Validator::sanitizeString($_POST['segundo_nombre']);
            $colaborador->primer_apellido = Validator::sanitizeString($_POST['primer_apellido']);
            $colaborador->segundo_apellido = Validator::sanitizeString($_POST['segundo_apellido']);
            $colaborador->sexo = $_POST['sexo'];
            $colaborador->identificacion = $identificacion_sanitizada;
            $colaborador->fecha_nacimiento = $_POST['fecha_nacimiento'];
            $colaborador->correo_personal = Validator::sanitizeEmail($_POST['correo_personal']);
            $colaborador->telefono = Validator::sanitizeAlphaNumeric($_POST['telefono']);
            $colaborador->celular = Validator::sanitizeAlphaNumeric($_POST['celular']);
            $colaborador->direccion = Validator::sanitizeString($_POST['direccion']);
            $colaborador->estatus = Validator::sanitizeString($_POST['estatus']);

            // 4. Manejar la subida de la foto
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/fotos/';
                $fileName = uniqid() . '-' . basename($_FILES['foto_perfil']['name']);
                $targetFile = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $targetFile)) {
                    $colaborador->foto_perfil = $fileName;
                }
            } else {
                $datos_actuales = Colaborador::findById($_POST['id']);
                $colaborador->foto_perfil = $datos_actuales['foto_perfil'];
            }

            // Inicio Lógica de Historial Académico
            if (isset($_FILES['historial_academico_pdf']) && $_FILES['historial_academico_pdf']['error'] === UPLOAD_ERR_OK) {
                $uploadDirPdf = __DIR__ . '/../../public/uploads/pdf/';
                $fileNamePdf = uniqid() . '-' . basename($_FILES['historial_academico_pdf']['name']);
                $targetFilePdf = $uploadDirPdf . $fileNamePdf;

                if (move_uploaded_file($_FILES['historial_academico_pdf']['tmp_name'], $targetFilePdf)) {
                    $colaborador->historial_academico_pdf = $fileNamePdf;
                }
            } else {
                // Si no se sube un nuevo PDF, mantenemos el existente.
                $colaborador->historial_academico_pdf = isset($colaborador->historial_academico_pdf) ? $colaborador->historial_academico_pdf : null;
            }
            // Fin Lógica de Historial Académico
            if ($colaborador->actualizar()) {
                header('Location: ' . BASE_PATH . '/colaboradores');
                exit();
            } else {
                // Este es el error que veías antes
                die("Error final: El modelo recibió los datos pero no pudo actualizar la base de datos.");
            }
        }
    }

    /**
     * Almacena un nuevo colaborador en la base de datos.
     * Incluye validación de datos y manejo de archivos (foto y PDF).
     *
     * @return void Redirige según el resultado de la operación
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $errors = [];
            if (!Validator::validateRequired($_POST['primer_nombre'])) {
                $errors[] = "El primer nombre es obligatorio.";
            }
            if (!Validator::validateRequired($_POST['primer_apellido'])) {
                $errors[] = "El primer apellido es obligatorio.";
            }
            if (!Validator::validateRequired($_POST['identificacion'])) {
                $errors[] = "La identificación es obligatoria.";
            }
            if (!Validator::validatePanamanianID($_POST['identificacion'])) {
                $errors[] = "La identificación no es válida.";
            }
            if (!Validator::validateRequired($_POST['fecha_nacimiento'])) {
                $errors[] = "La fecha de nacimiento es obligatoria.";
            }
            if (!Validator::validateRequired($_POST['correo_personal'])) {
                $errors[] = "El correo electrónico es obligatorio.";
            }
            if (!Validator::validateEmail($_POST['correo_personal'])) {
                $errors[] = "El formato del correo electrónico no es válido.";
            }

            if (!empty($errors)) {
                // Si hay errores, guardarlos en la sesión y redirigir al formulario
                $_SESSION['errors'] = $errors;
                header('Location: ' . BASE_PATH . '/colaboradores/crear');
                exit();
            }

            $colaborador = new Colaborador();
            $colaborador->primer_nombre = Validator::sanitizeString($_POST['primer_nombre']);
            $colaborador->primer_apellido = Validator::sanitizeString($_POST['primer_apellido']);
            if (isset($_POST['segundo_nombre'])) {
                $colaborador->segundo_nombre = Validator::sanitizeString($_POST['segundo_nombre']);
            } else {
                $colaborador->segundo_nombre = null; // O un valor por defecto
            }
            if (isset($_POST['segundo_apellido'])) {
                $colaborador->segundo_apellido = Validator::sanitizeString($_POST['segundo_apellido']);
            } else {
                $colaborador->segundo_apellido = null; // O un valor por defecto
            }
            $colaborador->sexo = $_POST['sexo'];
            $colaborador->identificacion = Validator::sanitizeAlphaNumeric($_POST['identificacion']);
            $colaborador->fecha_nacimiento = $_POST['fecha_nacimiento'];

            // ---- INICIO LÓGICA DE FOTO ----
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/fotos/';
                // Crea un nombre único para el archivo para evitar colisiones
                $fileName = uniqid() . '-' . basename($_FILES['foto_perfil']['name']);
                $targetFile = $uploadDir . $fileName;

                // Mueve el archivo temporal a la carpeta de destino
                if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $targetFile)) {
                    $colaborador->foto_perfil = $fileName;
                } else {
                    $colaborador->foto_perfil = null; // O manejar el error
                }
            }


            $colaborador->correo_personal = Validator::sanitizeEmail($_POST['correo_personal']);
            $colaborador->telefono = Validator::sanitizeAlphaNumeric($_POST['telefono']);
            $colaborador->celular = Validator::sanitizeAlphaNumeric($_POST['celular']);
            $colaborador->direccion = Validator::sanitizeString($_POST['direccion']);
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