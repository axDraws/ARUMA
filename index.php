<?php
// index.php - Router principal del proyecto

require_once __DIR__ . '/app/config.php';

// Obtener la ruta solicitada
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Tabla de rutas
switch ($uri) {

    // Página principal
    case '':
    case 'home':
        require __DIR__ . '/views/home.php';
        break;

    // Cliente
    case 'cliente':
        require __DIR__ . '/views/cliente.php';
        break;

    // Administrador - Dashboard
    case 'administrador':
        require_once __DIR__ . '/Controller/AdminController.php';
        $adminController = new AdminController();
        $adminController->dashboard();
        break;

    // Administrador - Vista de reservas (HTML completa)
    case 'admin/reservas':
        require_once __DIR__ . '/Controller/AdminController.php';
        $adminController = new AdminController();
        $adminController->reservas();
        break;

    // Misión / Visión
    case 'mision-vision':
        require __DIR__ . '/views/mision-vision.php';
        break;

    // Reservas (MVC) - Para clientes
    case 'reservas':
        require_once __DIR__ . '/Controller/ReservationController.php';
        $ctrl = new ReservationController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->store();
        } else {
            $ctrl->index();
        }
        break;

    // === RUTAS API PARA ADMIN ===

    // API: Obtener todas las reservas (JSON)
    case 'api/reservas':
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
        require_once __DIR__ . '/Controller/AdminController.php';
        $adminController = new AdminController();
        $adminController->getReservasApi();
        break;

    // API: Obtener una reserva específica
    case 'api/reservas/show':
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
        require_once __DIR__ . '/Controller/AdminController.php';
        $adminController = new AdminController();
        $adminController->getReservaByIdApi();
        break;

    // API: Crear nueva reserva
    case 'api/reservas/create':
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
        require_once __DIR__ . '/Controller/AdminController.php';
        $adminController = new AdminController();
        $adminController->createReservaApi();
        break;

    // API: Actualizar reserva
    case 'api/reservas/update':
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
        require_once __DIR__ . '/Controller/AdminController.php';
        $adminController = new AdminController();
        $adminController->updateReservaApi();
        break;

    // API: Eliminar reserva
    case 'api/reservas/delete':
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
        require_once __DIR__ . '/Controller/AdminController.php';
        $adminController = new AdminController();
        $adminController->deleteReservaApi();
        break;

    // Login
    case 'login':
        require_once __DIR__ . '/Controller/AuthController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AuthController())->login();
        }
        break;

    // Registro
    case 'register':
        require_once __DIR__ . '/Controller/AuthController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AuthController())->register();
        }
        break;

    // Logout
    case 'logout':
        session_start();
        session_destroy();
        header("Location: /");
        break;

    // 404
    default:
        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        echo "<p>La ruta: <strong>$uri</strong> no existe.</p>";
        break;
}
