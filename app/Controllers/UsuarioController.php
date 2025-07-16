<?php

namespace App\Controllers;

use App\Models\Usuario;

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
                header('Location: /SemestralDesVII/CapitalHumano/public/login');
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
            if ($user && password_verify($password, $user['password_hash'])) {

                // Iniciar sesión y guardar datos del usuario
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_rol'] = $user['rol_id'];

                // Redirigir a una página de bienvenida o dashboard
                header('Location: /SemestralDesVII/CapitalHumano/public/dashboard');
            } else {
                // Si las credenciales son incorrectas, redirigir de vuelta al login con un error
                header('Location: /SemestralDesVII/CapitalHumano/public/login?error=1');
            }
                exit();
            }
    }

}