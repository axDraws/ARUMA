<?php
require_once __DIR__ . '/../Model/HistoryModel.php';

class AdminController {

    private $historyModel;

    public function __construct() {
        $this->historyModel = new HistoryModel();
    }

    /* ============================================================
        VALIDAR ADMIN
    ============================================================ */
    private function checkAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }
        return true;
    }

    /* ============================================================
        REGISTRAR ACCIÓN EN HISTORIAL
    ============================================================ */
  private function logAdminAction($evento, $detalle = null, $reservation_id = null, 
                                $anterior_valor = null, $nuevo_valor = null) {
    $user_id = $_SESSION['user_id'] ?? null;
    
    // VERIFICACIÓN: Asegurarnos de que $evento no sea null
    if (empty($evento)) {
        error_log("Error: Evento vacío en logAdminAction()");
        $evento = 'Acción desconocida';
    }
    
    // VERIFICACIÓN: Asegurarnos de que $user_id sea un número o null
    if ($user_id !== null && !is_numeric($user_id)) {
        error_log("Error: user_id no es numérico en logAdminAction(): " . print_r($user_id, true));
        $user_id = null;
    }
    
    $this->historyModel->log(
        $evento,           // 1. evento (string) - ¡NO debe ser null!
        $reservation_id,   // 2. reservation_id (int/null)
        $user_id,          // 3. user_id (int/null) - Debe ser número o null
        $detalle,          // 4. detalle (string/null)
        'reserva',         // 5. tipo_evento (string)
        $anterior_valor,   // 6. anterior_valor (array/null)
        $nuevo_valor       // 7. nuevo_valor (array/null)
    );
} 
   /* ============================================================
        DASHBOARD
    ============================================================ */
    public function dashboard() {
        $this->checkAdmin();

        require_once __DIR__ . '/../Model/AdminModel.php';
        $adminModel = new AdminModel();
        $stats = $adminModel->getDashboardStats();

        $reservas_hoy         = $stats['reservas_hoy'];
        $reservas_confirmadas = $stats['reservas_confirmadas'];
        $reservas_pendientes  = $stats['reservas_pendientes'];
        $total_clientes       = $stats['total_clientes'];
        $reservas_detalle     = $stats['reservas_hoy_detalle'];

        require_once __DIR__ . '/../Model/ClientModel.php';
        require_once __DIR__ . '/../Model/ServiceModel.php';
        require_once __DIR__ . '/../Model/TherapistModel.php';

        $clientes   = (new ClientModel())->getAllClients();
        $servicios  = (new ServiceModel())->getAllServices();
        $terapeutas = (new TherapistModel())->getAllTherapists();

        require_once __DIR__ . '/../views/administrador.php';
    }

    /* ============================================================
        HISTORIAL
    ============================================================ */
    public function historial() {
        $this->checkAdmin();

        require_once __DIR__ . '/../Model/AdminModel.php';
        $adminModel = new AdminModel();

        // OBTENER HISTORIAL
        $historial = $adminModel->getHistorial();

        require_once __DIR__ . '/../views/historial.php';
    }

    /* ============================================================
        VISTA PRINCIPAL DE RESERVAS
    ============================================================ */
    public function reservas() {
        $this->checkAdmin();

        require_once __DIR__ . '/../Model/ReservationModel.php';
        require_once __DIR__ . '/../Model/ServiceModel.php';
        require_once __DIR__ . '/../Model/TherapistModel.php';
        require_once __DIR__ . '/../Model/ClientModel.php';

        $reservationModel = new ReservationModel();

        $reservas   = $reservationModel->all();
        $servicios  = (new ServiceModel())->getAllServices();
        $terapeutas = (new TherapistModel())->getAllTherapists();
        $clientes   = (new ClientModel())->getAllClients();

        require_once __DIR__ . '/../views/administrador.php';
    }

    /* ============================================================
        API: TODAS LAS RESERVAS
    ============================================================ */
    public function getReservasApi() {
        $this->checkAdmin();
        header('Content-Type: application/json');

        try {
            require_once __DIR__ . '/../Model/ReservationModel.php';
            echo json_encode((new ReservationModel())->all());
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener reservas']);
        }
    }

    /* ============================================================
        API: RESERVA POR ID
    ============================================================ */
    public function getReservaByIdApi() {
        $this->checkAdmin();
        header('Content-Type: application/json');

        if (empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID no proporcionado']);
            return;
        }

        try {
            require_once __DIR__ . '/../Model/ReservationModel.php';
            $reserva = (new ReservationModel())->find($_GET['id']);

            if ($reserva) {
                echo json_encode($reserva);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Reserva no encontrada']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno']);
        }
    }

    /* ============================================================
        API: CREAR RESERVA
    ============================================================ */
    public function createReservaApi() {
        $this->checkAdmin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $required = ['cliente_id', 'servicio_id', 'fecha', 'hora'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                http_response_code(400);
                echo json_encode(['error' => "El campo '$field' es requerido"]);
                return;
            }
        }

        try {
            require_once __DIR__ . '/../Model/ReservationModel.php';
            require_once __DIR__ . '/../Model/ClientModel.php';
            require_once __DIR__ . '/../Model/ServiceModel.php';

            $reservationModel = new ReservationModel();
            
            // Obtener información del cliente y servicio para el log
            $clientModel = new ClientModel();
            $serviceModel = new ServiceModel();
            
            $cliente = $clientModel->find($_POST['cliente_id']);
            $servicio = $serviceModel->find($_POST['servicio_id']);
            
            $data = [
                'cliente_id'   => $_POST['cliente_id'],
                'servicio_id'  => $_POST['servicio_id'],
                'therapist_id' => $_POST['therapist_id'] ?? null,
                'fecha'        => $_POST['fecha'],
                'hora'         => $_POST['hora'],
                'estado'       => $_POST['estado'] ?? 'Pendiente',
                'notas'        => $_POST['notas'] ?? '',
                'cliente_nombre' => $cliente['nombre'] ?? '',
                'servicio_nombre' => $servicio['nombre'] ?? ''
            ];

            $id = $reservationModel->create($data);

            if ($id) {
                // Registrar acción en historial
                $detalle = "Reserva creada por administrador | Cliente: " . ($cliente['nombre'] ?? 'N/A') . 
                          " | Servicio: " . ($servicio['nombre'] ?? 'N/A') . 
                          " | Fecha: " . $_POST['fecha'] . " | Hora: " . $_POST['hora'];
                
                $this->logAdminAction(
                    'Reserva Creada (Admin)',
                    $detalle,
                    $id
                );
            }

            echo json_encode([
                'status' => $id ? 'ok' : 'error',
                'id'     => $id
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    /* ============================================================
        API: ACTUALIZAR RESERVA
    ============================================================ */
    public function updateReservaApi() {
        $this->checkAdmin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        if (empty($_POST['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID no especificado']);
            return;
        }

        try {
            require_once __DIR__ . '/../Model/ReservationModel.php';
            require_once __DIR__ . '/../Model/ClientModel.php';
            require_once __DIR__ . '/../Model/ServiceModel.php';
            require_once __DIR__ . '/../Model/TherapistModel.php';

            $reservationModel = new ReservationModel();
            $clientModel = new ClientModel();
            $serviceModel = new ServiceModel();
            $therapistModel = new TherapistModel();

            $reserva = $reservationModel->find($_POST['id']);
            if (!$reserva) {
                http_response_code(404);
                echo json_encode(['error' => 'Reserva no encontrada']);
                return;
            }

            // Preparar datos antiguos para el log
            $anterior_valor = [
                'cliente_id' => $reserva['cliente_id'],
                'servicio_id' => $reserva['servicio_id'],
                'therapist_id' => $reserva['therapist_id'],
                'fecha' => $reserva['fecha'],
                'hora' => $reserva['hora'],
                'estado' => $reserva['estado'],
                'notas' => $reserva['notas']
            ];

            $data = [
                'cliente_id'   => $_POST['cliente_id'] ?? $reserva['cliente_id'],
                'servicio_id'  => $_POST['servicio_id'] ?? $reserva['servicio_id'],
                'therapist_id' => $_POST['therapist_id'] ?? $reserva['therapist_id'],
                'fecha'        => $_POST['fecha'] ?? $reserva['fecha'],
                'hora'         => $_POST['hora'] ?? $reserva['hora'],
                'estado'       => $_POST['estado'] ?? $reserva['estado'],
                'notas'        => $_POST['notas'] ?? $reserva['notas']
            ];

            // Preparar datos nuevos para el log
            $nuevo_valor = [
                'cliente_id' => $data['cliente_id'],
                'servicio_id' => $data['servicio_id'],
                'therapist_id' => $data['therapist_id'],
                'fecha' => $data['fecha'],
                'hora' => $data['hora'],
                'estado' => $data['estado'],
                'notas' => $data['notas']
            ];

            $ok = $reservationModel->update($_POST['id'], $data);

            if ($ok) {
                // Registrar acción en historial
                $cambios = [];
                
                if ($anterior_valor['estado'] != $nuevo_valor['estado']) {
                    $cambios[] = "Estado: " . ucfirst($anterior_valor['estado']) . " → " . ucfirst($nuevo_valor['estado']);
                }
                
                if ($anterior_valor['fecha'] != $nuevo_valor['fecha'] || $anterior_valor['hora'] != $nuevo_valor['hora']) {
                    $cambios[] = "Fecha/Hora: " . $anterior_valor['fecha'] . " " . $anterior_valor['hora'] . 
                                 " → " . $nuevo_valor['fecha'] . " " . $nuevo_valor['hora'];
                }
                
                if ($anterior_valor['therapist_id'] != $nuevo_valor['therapist_id']) {
                    $cambios[] = "Terapeuta cambiado";
                }
                
                if ($anterior_valor['notas'] != $nuevo_valor['notas']) {
                    $cambios[] = "Notas actualizadas";
                }
                
                $detalle = !empty($cambios) ? implode(" | ", $cambios) : "Reserva actualizada sin cambios específicos";
                
                $this->logAdminAction(
                    'Reserva Actualizada (Admin)',
                    $detalle,
                    $_POST['id'],
                    $anterior_valor,
                    $nuevo_valor
                );
            }

            echo json_encode([
                'status' => $ok ? 'ok' : 'error'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    /* ============================================================
        API: ELIMINAR RESERVA
    ============================================================ */
    public function deleteReservaApi() {
        $this->checkAdmin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        if (empty($_POST['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
            return;
        }

        try {
            require_once __DIR__ . '/../Model/ReservationModel.php';
            require_once __DIR__ . '/../Model/ClientModel.php';
            require_once __DIR__ . '/../Model/ServiceModel.php';

            $reservationModel = new ReservationModel();
            $clientModel = new ClientModel();
            $serviceModel = new ServiceModel();

            $reserva = $reservationModel->find($_POST['id']);
            if (!$reserva) {
                http_response_code(404);
                echo json_encode(['error' => 'No existe']);
                return;
            }

            // Obtener información para el log antes de eliminar
            $cliente = $clientModel->find($reserva['cliente_id']);
            $servicio = $serviceModel->find($reserva['servicio_id']);
            
            // Preparar datos antiguos para el log
            $anterior_valor = [
                'id' => $reserva['id'],
                'cliente_id' => $reserva['cliente_id'],
                'cliente_nombre' => $cliente['nombre'] ?? '',
                'servicio_id' => $reserva['servicio_id'],
                'servicio_nombre' => $servicio['nombre'] ?? '',
                'fecha' => $reserva['fecha'],
                'hora' => $reserva['hora'],
                'estado' => $reserva['estado']
            ];
            
            // Registrar eliminación en historial ANTES de eliminar
            $detalle = "Reserva eliminada por administrador | Cliente: " . ($cliente['nombre'] ?? 'N/A') . 
                      " | Servicio: " . ($servicio['nombre'] ?? 'N/A') . 
                      " | Fecha: " . $reserva['fecha'] . " | Hora: " . $reserva['hora'];
            
            $this->logAdminAction(
                'Reserva Eliminada (Admin)',
                $detalle,
                $_POST['id'],
                $anterior_valor,
                null
            );

            // Ahora eliminar la reserva
            $ok = $reservationModel->delete($_POST['id']);

            echo json_encode([
                'status' => $ok ? 'ok' : 'error'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    /* ============================================================
        API: HORARIOS DISPONIBLES
    ============================================================ */
    public function getHorariosDisponibles() {
        $this->checkAdmin();
        header('Content-Type: application/json');

        if (!isset($_GET['fecha'])) {
            echo json_encode([]);
            return;
        }

        echo json_encode([
            '09:00', '10:00', '11:00', '12:00',
            '13:00', '14:00', '15:00', '16:00',
            '17:00', '18:00'
        ]);
    }

    /* ============================================================
        REGISTRAR LOGIN DE ADMINISTRADOR
       (Debes llamar a este método desde tu AuthController)
    ============================================================ */
    public function logAdminLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            $detalle = "Administrador inició sesión desde IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Desconocida');
            
            $this->logAdminAction(
                'Login Administrador',
                $detalle,
                null,
                null,
                null
            );
        }
    }

    /* ============================================================
        REGISTRAR LOGOUT DE ADMINISTRADOR
       (Debes llamar a este método desde tu logout)
    ============================================================ */
    public function logAdminLogout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            $detalle = "Administrador cerró sesión";
            
            $this->logAdminAction(
                'Logout Administrador',
                $detalle,
                null,
                null,
                null
            );
        }
    }
}
