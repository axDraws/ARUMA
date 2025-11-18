<?php
// index.php - Router principal del proyecto

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/Controller/ReservationController.php';
require_once __DIR__ . '/Controller/AuthController.php';

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

    // Administrador
    case 'administrador':
        require __DIR__ . '/views/administrador.php';
        break;

    // Misión / Visión
    case 'mision-vision':
        require __DIR__ . '/views/mision-vision.php';
        break;

    // Reservas (MVC)
    case 'reservas':
        $ctrl = new ReservationController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->store();
        } else {
            $ctrl->index();
        }
        break;

    // Login
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AuthController())->login();
        }
        break;

    // Registro
    case 'register':
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
