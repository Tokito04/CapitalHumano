<?php

// Incluimos el autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar las variables de entorno desde el archivo .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

use App\Controllers\UsuarioController;
use App\Controllers\ColaboradorController;
use App\Controllers\CargoController;
use App\Controllers\ReporteController;
use App\Controllers\VacacionesController;
use App\Controllers\Api\ApiController;
use App\Helpers\AuthHelper;
use App\Helpers\CsrfHelper;
use App\Controllers\ErrorController;

// --- Cabeceras de seguridad HTTP (aplican a toda respuesta) ---
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' https://cdn.jsdelivr.net; object-src 'none'; base-uri 'self'; frame-ancestors 'none'");

// --- Sesión endurecida: cookie httponly/samesite, secure si la conexión es HTTPS ---
$esHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $esHttps,
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();

// --- Validación CSRF centralizada para toda solicitud que modifique estado ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !CsrfHelper::validar()) {
    http_response_code(403);
    echo 'Solicitud inválida o expirada (token CSRF ausente o incorrecto). Recargue la página e intente de nuevo.';
    exit();
}

// --- INICIO DEL ROUTER MEJORADO ---
$base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
define('BASE_PATH', $base_path);
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
        header('Location:'.BASE_PATH. '/dashboard');
        exit();

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

    case '/dashboard':
        AuthHelper::exigirSesion();
        require_once __DIR__ . '/../views/dashboard.php';
        break;

    case '/logout':
        AuthHelper::exigirSesion();
        $_SESSION = [];
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
        header('Location:'.BASE_PATH. '/login');
        exit();

    case '/colaboradores':
        AuthHelper::exigirSesion();
        $controller = new ColaboradorController();
        $controller->index();
        break;

    case '/colaboradores/crear':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        $controller = new ColaboradorController();
        $controller->showCreateForm();
        break;

    case '/colaboradores/store':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        if ($method === 'POST') {
            $controller = new ColaboradorController();
            $controller->store();
        }
        break;

    case '/colaboradores/editar':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        $controller = new ColaboradorController();
        $controller->showEditForm();
        break;

    case '/colaboradores/update':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        if ($method === 'POST') {
            $controller = new ColaboradorController();
            $controller->update();
        }
        break;

    case '/cargos/crear':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        $controller = new CargoController();
        $controller->showCreateForm();
        break;

    case '/cargos/store':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        if ($method === 'POST') {
            $controller = new CargoController();
            $controller->store();
        }
        break;

    case '/reportes/colaboradores':
        AuthHelper::exigirSesion();
        $controller = new ReporteController();
        $controller->colaboradores();
        break;

    case '/reportes/colaboradores/exportar':
        AuthHelper::exigirSesion();
        $controller = new ReporteController();
        $controller->exportarColaboradores();
        break;

    case '/vacaciones':
        AuthHelper::exigirSesion();
        $controller = new VacacionesController();
        $controller->index();
        break;

    case '/vacaciones/generar':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        $controller = new VacacionesController();
        $controller->generarResuelto();
        break;

    case '/usuarios':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        $controller = new UsuarioController();
        $controller->index();
        break;

    case '/usuarios/editar':
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        $controller = new UsuarioController();
        $controller->showEditForm();
        break;

    case '/usuarios/update':
        // Corregido: esta ruta modifica el rol/estado de cualquier usuario,
        // por lo tanto requiere rol de administrador (antes solo exigía sesión).
        AuthHelper::verificarPermiso(AuthHelper::ROL_ADMINISTRADOR);
        if ($method === 'POST') {
            $controller = new UsuarioController();
            $controller->update();
        }
        break;

    // --- RUTAS DE LA API ---
    // Requieren sesión activa (uso interno del dashboard) o una API Key
    // válida (consumo externo, p.ej. Contraloría). Ver ApiController.
    case '/api/colaboradores/stats/sexo':
        $controller = new ApiController();
        $controller->estadisticasSexo();
        break;

    case '/api/dashboard/stats':
        $controller = new ApiController();
        $controller->estadisticasGenerales();
        break;

    case '/timer':
        header('Location:' . BASE_PATH . '/timer.html');
        exit();

    default:
        $controller = new ErrorController();
        $controller->notFound();
        break;
}
