<?php
require_once __DIR__ . '/../Model/ReservationModel.php';
require_once __DIR__ . '/../Model/HistoryModel.php';

class ReservationController
{
    private $reservationModel;
    private $historyModel;

    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
        $this->historyModel = new HistoryModel();
    }

    /* ============================================================
        VALIDAR SESIÓN
    ============================================================ */
    private function checkSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
    }

    /* ============================================================
        API: OBTENER MIS RESERVAS (CLIENTE)
    ============================================================ */
    public function getMyReservationsApi()
    {
        $this->checkSession();
        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'];

        // Como el modelo no tiene un método específico getMyReservations, 
        // traeremos todas y filtraremos (o idealmente agregaríamos un método al modelo).
        // Por ahora, filtramos aquí.
        // TODO: Agregar getByClientId en ReservationModel para mejor performance.

        $reservations = $this->reservationModel->all();
        $myReservations = array_filter($reservations, function ($res) use ($userId) {
            return $res['cliente_id'] == $userId;
        });

        echo json_encode(array_values($myReservations));
    }

    /* ============================================================
        API: CREAR RESERVA (CLIENTE)
    ============================================================ */
    public function createReservationApi()
    {
        $this->checkSession();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Validar datos mínimos
        if (empty($_POST['servicio_id']) || empty($_POST['fecha']) || empty($_POST['hora'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Servicio, fecha y hora son requeridos']);
            return;
        }

        $data = [
            'cliente_id' => $_SESSION['user_id'],
            'servicio_id' => $_POST['servicio_id'],
            'therapist_id' => $_POST['therapist_id'] ?? null, // Opcional o asignado por admin
            'fecha' => $_POST['fecha'],
            'hora' => $_POST['hora'],
            'duracion_min' => 60, // Podría venir del servicio seleccionad
            'estado' => 'pendiente',
            'notas' => $_POST['notas'] ?? ''
        ];

        /* 
           NOTA: En una implementación real, deberíamos validar:
           1. Que el servicio exista.
           2. Que el terapeuta esté disponible (si se seleccionó).
           3. Que no haya conflictos de horario.
           4. Obtener duración real del servicio.
        */

        $id = $this->reservationModel->create($data);

        if ($id) {
            echo json_encode(['status' => 'ok', 'id' => $id]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear la reserva']);
        }
    }

    /* ============================================================
        API: CANCELAR RESERVA (CLIENTE)
    ============================================================ */
    public function cancelReservationApi()
    {
        $this->checkSession();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        if (empty($_POST['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de reserva requerido']);
            return;
        }

        $reservaId = $_POST['id'];
        $userId = $_SESSION['user_id'];

        // Verificar que la reserva pertenezca al usuario
        $reserva = $this->reservationModel->find($reservaId);

        if (!$reserva || $reserva['cliente_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para cancelar esta reserva']);
            return;
        }

        if ($reserva['estado'] === 'cancelada') {
            echo json_encode(['status' => 'ok', 'message' => 'La reserva ya estaba cancelada']);
            return;
        }

        // Cancelamos actualizando estado
        $ok = $this->reservationModel->updateEstado($reservaId, 'cancelada');

        if ($ok) {
            echo json_encode(['status' => 'ok']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al cancelar la reserva']);
        }
    }
}
