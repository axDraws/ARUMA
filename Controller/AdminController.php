<?php
class AdminController {
    
private function checkAdmin() {
    // Asegurar que la sesión esté iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: /");
        exit();
    }
    return true;
}
    
    // Dashboard principal
    public function dashboard() {
        $this->checkAdmin();
        
        // Obtener estadísticas
        require_once __DIR__ . '/../Model/AdminModel.php';
        $adminModel = new AdminModel();
        $stats = $adminModel->getDashboardStats();
        
        // Pasar datos a la vista
        $reservas_hoy = $stats['reservas_hoy'];
        $reservas_confirmadas = $stats['reservas_confirmadas'];
        $reservas_pendientes = $stats['reservas_pendientes'];
        $total_clientes = $stats['total_clientes'];
        $reservas_detalle = $stats['reservas_hoy_detalle'];
        
        // Cargar la vista con los datos
        require_once __DIR__ . '/../views/administrador.php';
    }
    
    // Vista completa de reservas (para la ruta admin/reservas) - MÉTODO ÚNICO
    public function reservas() {
        $this->checkAdmin();
        
        // Obtener datos para los selects
        require_once __DIR__ . '/../Model/ReservationModel.php';
        require_once __DIR__ . '/../Model/ServiceModel.php';
        require_once __DIR__ . '/../Model/TherapistModel.php';
        require_once __DIR__ . '/../Model/ClientModel.php';
        
        $reservationModel = new ReservationModel();
        $serviceModel = new ServiceModel();
        $therapistModel = new TherapistModel();
        $clientModel = new ClientModel();
        
        // Pasar datos a la vista
        $reservas = $reservationModel->all();
        $servicios = $serviceModel->getAllServices();
        $terapeutas = $therapistModel->getAllTherapists();
        $clientes = $clientModel->getAllClients();
        
        // Cargar la vista con los datos
        require_once __DIR__ . '/../views/administrador.php';
    }
    
    // ============================================
    // MÉTODOS API (JSON)
    // ============================================
    
    // API: Obtener todas las reservas
    public function getReservasApi() {
        $this->checkAdmin();
        header('Content-Type: application/json');
        
        try {
            require_once __DIR__ . '/../Model/ReservationModel.php';
            $reservationModel = new ReservationModel();
            $reservas = $reservationModel->all();
            
            echo json_encode($reservas);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener reservas: ' . $e->getMessage()]);
        }
    }
    
    // API: Obtener una reserva por ID
    public function getReservaByIdApi() {
        $this->checkAdmin();
        header('Content-Type: application/json');
        
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de reserva no proporcionado']);
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
            echo json_encode(['error' => 'Error al obtener la reserva: ' . $e->getMessage()]);
        }
    }
    
    // API: Crear nueva reserva
    public function createReservaApi() {
        $this->checkAdmin();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        
        // Validar datos requeridos
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
            
            $data = [
                'cliente_id' => $_POST['cliente_id'],
                'servicio_id' => $_POST['servicio_id'],
                'therapist_id' => $_POST['therapist_id'] ?? null,
                'fecha' => $_POST['fecha'],
                'hora' => $_POST['hora'],
                'estado' => $_POST['estado'] ?? 'Pendiente',
                'notas' => $_POST['notas'] ?? ''
            ];
            
            $id = $reservationModel->create($data);
            
            if ($id) {
                echo json_encode([
                    'status' => 'ok',
                    'message' => 'Reserva creada exitosamente',
                    'id' => $id
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al crear la reserva']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear reserva: ' . $e->getMessage()]);
        }
    }
    
    // API: Actualizar reserva
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
            echo json_encode(['error' => 'ID de reserva no proporcionado']);
            return;
        }
        
        try {
            require_once __DIR__ . '/../Model/ReservationModel.php';
            $reservationModel = new ReservationModel();
            
            // Verificar que la reserva existe
            $reserva = $reservationModel->find($_POST['id']);
            if (!$reserva) {
                http_response_code(404);
                echo json_encode(['error' => 'Reserva no encontrada']);
                return;
            }
            
            $data = [
                'cliente_id' => $_POST['cliente_id'] ?? $reserva['clients_id'],
                'servicio_id' => $_POST['servicio_id'] ?? $reserva['service_id'],
                'therapist_id' => $_POST['therapist_id'] ?? $reserva['therapist_id'],
                'fecha' => $_POST['fecha'] ?? $reserva['fecha'],
                'hora' => $_POST['hora'] ?? $reserva['hora'],
                'estado' => $_POST['estado'] ?? $reserva['estado'],
                'notas' => $_POST['notas'] ?? $reserva['notas']
            ];
            
            $success = $reservationModel->update($_POST['id'], $data);
            
            if ($success) {
                echo json_encode([
                    'status' => 'ok',
                    'message' => 'Reserva actualizada exitosamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al actualizar la reserva']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar reserva: ' . $e->getMessage()]);
        }
    }
    
    // API: Eliminar reserva
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
            echo json_encode(['error' => 'ID de reserva no proporcionado']);
            return;
        }
        
        try {
            require_once __DIR__ . '/../Model/ReservationModel.php';
            $reservationModel = new ReservationModel();
            
            // Verificar que la reserva existe
            $reserva = $reservationModel->find($_POST['id']);
            if (!$reserva) {
                http_response_code(404);
                echo json_encode(['error' => 'Reserva no encontrada']);
                return;
            }
            
            $success = $reservationModel->delete($_POST['id']);
            
            if ($success) {
                echo json_encode([
                    'status' => 'ok',
                    'message' => 'Reserva eliminada exitosamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al eliminar la reserva']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar reserva: ' . $e->getMessage()]);
        }
    }
    
    // API: Obtener horarios disponibles
    public function getHorariosDisponibles() {
        $this->checkAdmin();
        header('Content-Type: application/json');
        
        if (!isset($_GET['fecha'])) {
            echo json_encode([]);
            return;
        }
        
        try {
            // Horarios de trabajo del spa (9 AM a 7 PM)
            $horariosTrabajo = [
                '09:00', '10:00', '11:00', '12:00', 
                '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'
            ];
            
            echo json_encode($horariosTrabajo);
            
        } catch (Exception $e) {
            echo json_encode([]);
        }
    }
}
