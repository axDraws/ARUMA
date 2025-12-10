<?php
// index.php - Router principal del proyecto

require_once __DIR__ . '/app/config.php';

// Obtener la ruta solicitada
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// ---- FUNCION PARA PROTEGER APIs ADMIN ----
function adminApi($callback)
{
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado']);
        exit();
    }
    require_once __DIR__ . '/Controller/AdminController.php';
    $admin = new AdminController();
    $admin->$callback();
    exit();
}

// ---- FUNCION PARA PROTEGER APIs DE HISTORIAL ----
function historyApi($callback)
{
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado']);
        exit();
    }
    require_once __DIR__ . '/Controller/HistoryController.php';
    $history = new HistoryController();
    $history->$callback();
    exit();
}

switch ($uri) {

    /* ======================================
     * PÁGINAS PÚBLICAS / CLIENTE
     * ====================================== */

    case '':
    case 'home':
        require __DIR__ . '/views/home.php';
        break;

    case 'cliente':
        require __DIR__ . '/views/cliente.php';
        break;

    /* ======================================
     * ADMIN: Dashboard
     * ====================================== */

    case 'administrador':
        require_once __DIR__ . '/Controller/AdminController.php';
        (new AdminController())->dashboard();
        break;

    /* ======================================
     * ADMIN: Reservas
     * ====================================== */

    case 'admin/reservas':
        require_once __DIR__ . '/Controller/AdminController.php';
        (new AdminController())->reservas();
        break;

    /* ======================================
     * ADMIN: Historial (CORREGIDO)
     * ====================================== */

    case 'admin/historial':
        require_once __DIR__ . '/Controller/HistoryController.php';
        $historyController = new HistoryController();
        $historyController->index();
        break;

    /* ======================================
     * API ADMIN
     * ====================================== */

    case 'api/horarios-disponibles':
        adminApi('getHorariosDisponibles');
        break;

    case 'api/reservas':
        adminApi('getReservasApi');
        break;

    case 'api/reservas/show':
        adminApi('getReservaByIdApi');
        break;

    case 'api/reservas/create':
        adminApi('createReservaApi');
        break;

    case 'api/reservas/update':
        adminApi('updateReservaApi');
        break;

    case 'api/reservas/delete':
        adminApi('deleteReservaApi');
        break;

    /* ======================================
     * API HISTORIAL
     * ====================================== */

    case 'api/historial':
        historyApi('getHistorialApi');
        break;

    case 'api/historial/estadisticas':
        historyApi('getEstadisticasApi');
        break;

    case 'api/historial/limpiar':
        historyApi('limpiarHistorialApi');
        break;

    case 'api/historial/exportar':
        historyApi('exportarHistorialApi');
        break;

    case 'api/historial/evento':
        historyApi('getEventoByIdApi');
        break;

    /* ======================================
     * API CLIENTES (ADMIN)
     * ====================================== */

    // Función helper interna para clientes, similar a adminApi pero para ClientController
    // Se define aquí o se puede refactorizar. Por simplicidad, la simulo con un bloque.

    case 'api/clientes':
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        require_once __DIR__ . '/Controller/ClientController.php';
        (new ClientController())->getAllClientsApi();
        exit();

    case 'api/clientes/show':
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        require_once __DIR__ . '/Controller/ClientController.php';
        (new ClientController())->getClientByIdApi();
        exit();

    case 'api/clientes/create':
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        require_once __DIR__ . '/Controller/ClientController.php';
        (new ClientController())->createClientApi();
        exit();

    case 'api/clientes/update':
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        require_once __DIR__ . '/Controller/ClientController.php';
        (new ClientController())->updateClientApi();
        exit();

    case 'api/clientes/delete':
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        require_once __DIR__ . '/Controller/ClientController.php';
        (new ClientController())->deleteClientApi();
        exit();

    /* ======================================
     * CLIENTE: Reservas
     * ====================================== */

    case 'reservas':
        require_once __DIR__ . '/Controller/ReservationController.php';
        $ctrl = new ReservationController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->store();
        } else {
            $ctrl->index();
        }
        break;

    /* ======================================
     * LOGIN / REGISTER
     * ====================================== */

    case 'login':
        require_once __DIR__ . '/Controller/AuthController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AuthController())->login();
        }
        break;

    case 'register':
        require_once __DIR__ . '/Controller/AuthController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AuthController())->register();
        }
        break;

    case 'logout':
        session_start();
        session_destroy();
        header("Location: /");
        break;

    /* ======================================
     * 404 - NOT FOUND
     * ====================================== */

    default:
        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        echo "<p>La ruta: <strong>$uri</strong> no existe.</p>";
        break;
}
