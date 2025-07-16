<?php

session_start(); // Iniciamos sesión para usarla en el futuro

// Incluimos el autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar las variables de entorno desde el archivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '\..\\');
$dotenv->load();
define('BASE_PATH', '/SemestralDesVII/CapitalHumano/public');
use App\Controllers\UsuarioController;

// --- INICIO DEL ROUTER MEJORADO ---
$base_path = '/SemestralDesVII/CapitalHumano/public'; // Define el subdirectorio de tu proyecto
$request_uri = $_SERVER['REQUEST_URI'];

// Elimina el base_path de la URL solicitada
if (strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}

// Elimina query strings (ej. ?id=5)
$request_uri = strtok($request_uri, '?');

// Si la URI está vacía después de quitar el base_path, es la raíz
if (empty($request_uri)) {
    $request_uri = '/';
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($request_uri) {
    case '/':
        echo 'Página de Inicio';
        break;

    case '/login':
        $controller = new UsuarioController();
        if ($method === 'GET') {
            $controller->showLoginForm();
        } elseif ($method === 'POST') {
            $controller->processLogin();
        }
        break;

    case '/register':
        $controller = new UsuarioController();
        if ($method === 'GET') {
            $controller->showRegisterForm();
        } elseif ($method === 'POST') {
            $controller->register();
        }
        break;


    // ...
    case '/dashboard':
        // Proteger la ruta: solo accesible si hay una sesión activa
        if (!isset($_SESSION['user_id'])) {
            header('Location:'.BASE_PATH. '/login');
            exit();
        }
        require_once __DIR__ . '/../views/dashboard.php';
        break;

    case '/logout':
        session_unset();
        session_destroy();
        header('Location:'.BASE_PATH. '/login');
        exit();

    case '/colaboradores':
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        $controller = new App\Controllers\ColaboradorController();
        $controller->index();
        break;

    case '/colaboradores/crear':
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        $controller = new App\Controllers\ColaboradorController();
        $controller->showCreateForm();
        break;

    case '/colaboradores/store':
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        if ($method === 'POST') {
            $controller = new App\Controllers\ColaboradorController();
            $controller->store();
        }
        break;


    case '/colaboradores/editar':
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        $controller = new App\Controllers\ColaboradorController();
        $controller->showEditForm();
        break;


    case '/colaboradores/update':
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        if ($method === 'POST') {
            $controller = new App\Controllers\ColaboradorController();
            $controller->update();
        }
        break;

    // ... en el switch ($request_uri)

    case '/colaboradores/status':
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        if ($method === 'POST') {
            $controller = new App\Controllers\ColaboradorController();
            $controller->toggleStatus();
        }
        break;

    default:
        http_response_code(404);
        echo 'Página no encontrada';
        break;
}