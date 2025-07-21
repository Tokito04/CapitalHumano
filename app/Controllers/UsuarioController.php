<?php

namespace App\Controllers;

use App\Models\Usuario;
use App\Utils\Validator;
class UsuarioController
{
    /**
     * Muestra la vista del formulario de login.
     */
    public function showLoginForm()
    {
        // Carga la vista para el login
        require_once __DIR__ . '/../../views/auth/login.php';
    }

    /**
     * Muestra la vista del formulario de registro de un nuevo usuario.
     */
    public function showRegisterForm()
    {
        // Carga la vista para el registro
        require_once __DIR__ . '/../../views/auth/register.php';
    }

    /**
     * Procesa la solicitud de creación de un nuevo usuario.
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario();
            $usuario->nombre = $_POST['nombre'];
            $usuario->email = $_POST['email'];
            $usuario->password_hash = $_POST['password'];
            $usuario->rol_id = $_POST['rol_id']; // Asumiendo que viene del formulario

            if ($usuario->crear()) {
                // Redirigir al login o a una página de éxito
                header('Location:'.BASE_PATH. '/usuarios');
            } else {
                echo "Hubo un error al registrar el usuario.";
            }
        }
    }

    // Aquí irán los métodos para procesar el login, logout, etc.
    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Buscar usuario por email
            $user = Usuario::findByEmail($email);

            // Verificar si el usuario existe y si la contraseña es correcta
            // Esto cumple el requisito de "Implementar hash en las credenciales"
            if ($user && password_verify($password, $user['password_hash']) && $user['activo']) {

                // Iniciar sesión y guardar datos del usuario
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_rol'] = $user['rol_id'];

                // Redirigir a una página de bienvenida o dashboard
                header('Location:'.BASE_PATH. '/dashboard');
            } else {
                // Si las credenciales son incorrectas, redirigir de vuelta al login con un error
                header('Location:'.BASE_PATH. '/login?error=1');
            }
                exit();
            }
    }

    /**
     * Muestra la lista de todos los usuarios.
     */
    public function index()
    {
        $usuarios = Usuario::listarTodos();
        require_once __DIR__ . '/../../views/usuarios/index.php';
    }

    /**
     * Muestra el formulario para editar un usuario.
     */
    public function showEditForm()
    {
        $id = $_GET['id'];
        $usuario = Usuario::findById($id); // Reutilizamos el método findById
        // Aquí podríamos obtener la lista de roles si queremos un dropdown dinámico
        require_once __DIR__ . '/../../views/usuarios/edit.php';
    }

    /**
     * Procesa la actualización de un usuario.
     */
    public function update()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Aquí iría la validación de los datos...

            $usuario = new Usuario();
            $usuario->id = $_POST['id'];
            $usuario->nombre = Validator::sanitizeString($_POST['nombre']);
            $usuario->email = Validator::sanitizeEmail($_POST['email']);
            $usuario->rol_id = $_POST['rol_id'];
            // El checkbox envía '1' si está marcado, si no, no envía nada.
            $usuario->activo = $_POST['activo'];

            if ($usuario->actualizar()) {
                header('Location: ' . BASE_PATH . '/usuarios');
                exit();
            }
        }
    }
}