<?php
class AdminController {

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

    // ============================================================
    //  DASHBOARD
    // ============================================================
   
public function dashboard() {
    $this->checkAdmin();

    require_once __DIR__ . '/../Model/AdminModel.php';
    $adminModel = new AdminModel();
    $stats = $adminModel->getDashboardStats();

    // Variables para la vista
    $reservas_hoy          = $stats['reservas_hoy'];
    $reservas_confirmadas  = $stats['reservas_confirmadas'];
    $reservas_pendientes   = $stats['reservas_pendientes'];
    $total_clientes        = $stats['total_clientes'];
    $reservas_detalle      = $stats['reservas_hoy_detalle'];

    require_once __DIR__ . '/../Model/ClientModel.php';
    require_once __DIR__ . '/../Model/ServiceModel.php';
    require_once __DIR__ . '/../Model/TherapistModel.php';

    $clientModel    = new ClientModel();
    $serviceModel   = new ServiceModel();
    $therapistModel = new TherapistModel();

    $clientes   = $clientModel->getAllClients();
    $servicios  = $serviceModel->getAllServices();
    $terapeutas = $therapistModel->getAllTherapists();
    // ============================================

    require_once __DIR__ . '/../views/administrador.php';
}

    // ============================================================
    //  VISTA PRINCIPAL DE RESERVAS 
    // ============================================================
    public function reservas() {
        $this->checkAdmin();

        require_once __DIR__ . '/../Model/ReservationModel.php';
        require_once __DIR__ . '/../Model/ServiceModel.php';
        require_once __DIR__ . '/../Model/TherapistModel.php';
        require_once __DIR__ . '/../Model/ClientModel.php';

        $reservationModel = new ReservationModel();
        $serviceModel     = new ServiceModel();
        $therapistModel   = new TherapistModel();
        $clientModel      = new ClientModel();

        // Para la tabla
        $reservas   = $reservationModel->all();

        // Para el formulario: "Nueva Reserva"
        $servicios  = $serviceModel->getAllServices();
        $terapeutas = $therapistModel->getAllTherapists();
        $clientes   = $clientModel->getAllClients();

        require_once __DIR__ . '/../views/administrador.php';
    }

    // ============================================================
    //  API: OBTENER TODAS LAS RESERVAS
    // ============================================================
    public function getReservasApi() {
        $this->checkAdmin();
        header('Content-Type: application/json');

        try {
            require_once __DIR__ . '/../Model/ReservationModel.php';
            $reservationModel = new ReservationModel();
            echo json_encode($reservationModel->all());

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener reservas: ' . $e->getMessage()]);
        }
    }

    // ============================================================
    //  API: OBTENER RESERVA POR ID
    // ============================================================
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
            $reservationModel = new ReservationModel();
            $reserva = $reservationModel->find($_GET['id']);

            if ($reserva) {
                echo json_encode($reserva);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Reserva no encontrada']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    // ============================================================
    //  API: CREAR NUEVA RESERVA
    // ============================================================
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
            $reservationModel = new ReservationModel();

            // Deben coincidir EXACTO con ReservationModel::create()
            $data = [
                'cliente_id'   => $_POST['cliente_id'],
                'servicio_id'  => $_POST['servicio_id'],
                'therapist_id' => $_POST['therapist_id'] ?? null,
                'fecha'        => $_POST['fecha'],
                'hora'         => $_POST['hora'],
                'estado'       => $_POST['estado'] ?? 'Pendiente',
                'notas'        => $_POST['notas'] ?? ''
            ];

            $id = $reservationModel->create($data);

            if ($id) {
                echo json_encode([
                    'status'  => 'ok',
                    'message' => 'Reserva creada',
                    'id'      => $id
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo crear la reserva']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    // ============================================================
    //  API: ACTUALIZAR RESERVA
    // ============================================================
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
            $reservationModel = new ReservationModel();

            $reserva = $reservationModel->find($_POST['id']);
            if (!$reserva) {
                http_response_code(404);
                echo json_encode(['error' => 'Reserva no encontrada']);
                return;
            }

            $data = [
                'cliente_id'   => $_POST['cliente_id'] ?? $reserva['clients_id'],
                'servicio_id'  => $_POST['servicio_id'] ?? $reserva['service_id'],
                'therapist_id' => $_POST['therapist_id'] ?? $reserva['therapist_id'],
                'fecha'        => $_POST['fecha'] ?? $reserva['fecha'],
                'hora'         => $_POST['hora'] ?? $reserva['hora'],
                'estado'       => $_POST['estado'] ?? $reserva['estado'],
                'notas'        => $_POST['notas'] ?? $reserva['notas']
            ];

            $ok = $reservationModel->update($_POST['id'], $data);

            echo json_encode([
                'status'  => $ok ? 'ok' : 'error',
                'message' => $ok ? 'Reserva actualizada' : 'Error al actualizar'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // ============================================================
    //  API: ELIMINAR RESERVA
    // ============================================================
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
            echo json_encode(['error' => 'ID no proporcionado']);
            return;
        }

        try {
            require_once __DIR__ . '/../Model/ReservationModel.php';
            $reservationModel = new ReservationModel();

            $reserva = $reservationModel->find($_POST['id']);
            if (!$reserva) {
                http_response_code(404);
                echo json_encode(['error' => 'Reserva no existe']);
                return;
            }

            $ok = $reservationModel->delete($_POST['id']);

            echo json_encode([
                'status'  => $ok ? 'ok' : 'error',
                'message' => $ok ? 'Reserva eliminada' : 'Error al eliminar'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // ============================================================
    //  API: HORARIOS DISPONIBLES
    // ============================================================
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
}
