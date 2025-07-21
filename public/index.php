<?php

session_start(); // Iniciamos sesión para usarla en el futuro

// Incluimos el autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar las variables de entorno desde el archivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '\..\\');
$dotenv->load();
define('BASE_PATH', '/CapitalHumano/public');
use App\Controllers\UsuarioController;
use App\Controllers\ColaboradorController;
use App\Controllers\CargoController;
use App\Controllers\ReporteController;
use App\Controllers\VacacionesController;
use App\Controllers\Api\ApiController;
use App\Helpers\AuthHelper;
use App\Controllers\ErrorController;
// --- INICIO DEL ROUTER MEJORADO ---
$base_path = '/CapitalHumano/public'; // Define el subdirectorio de tu proyecto
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
        header('Location:'.BASE_PATH. '/dashboard');
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
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
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

        $controller = new ColaboradorController();
        $controller->index();
        break;

    case '/colaboradores/crear':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        $controller = new ColaboradorController();
        $controller->showCreateForm();
        break;

    case '/colaboradores/store':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        if ($method === 'POST') {
            $controller = new ColaboradorController();
            $controller->store();
        }
        break;


    case '/colaboradores/editar':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        $controller = new ColaboradorController();
        $controller->showEditForm();
        break;


    case '/colaboradores/update':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        if ($method === 'POST') {
            $controller = new ColaboradorController();
            $controller->update();
        }
        break;

    case '/colaboradores/status':
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        if ($method === 'POST') {
            $controller = new ColaboradorController();
            $controller->toggleStatus();
        }
        break;

    case '/cargos/crear':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        $controller = new CargoController();
        $controller->showCreateForm();
        break;

    case '/cargos/store':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        if ($method === 'POST') {
            $controller = new CargoController();
            $controller->store();
        }
        break;

    case '/reportes/colaboradores':
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        $controller = new ReporteController();
        $controller->colaboradores();
        break;

    case '/reportes/colaboradores/exportar':
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }

        $controller = new ReporteController();
        $controller->exportarColaboradores();
        break;

    case '/vacaciones':
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }
        $controller = new VacacionesController();
        $controller->index();
        break;

    case '/vacaciones/generar':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }
        $controller = new VacacionesController();
        $controller->generarResuelto();
        break;

    case '/usuarios':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }
        $controller = new UsuarioController();
        $controller->index();
        break;

    case '/usuarios/editar':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }
        $controller = new UsuarioController();
        $controller->showEditForm();
        break;

    case '/usuarios/update':
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); exit(); }
        if ($method === 'POST') {
            $controller = new UsuarioController();
            $controller->update();
        }
        break;

    // --- RUTA DE LA API ---
    case '/api/colaboradores/stats/sexo':
        $controller = new ApiController();
        $controller->estadisticasSexo();
        break;

    case '/api/dashboard/stats':
        $controller = new ApiController();
        $controller->estadisticasGenerales();
        break;

    default:
        $controller = new ErrorController();
        $controller->notFound();
        break;
}
