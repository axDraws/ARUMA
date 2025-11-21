<?php
require_once __DIR__ . '/../Model/ReservationModel.php';

class ReservationController {

    private ReservationModel $model;

    public function __construct() {
        $this->model = new ReservationModel();
    }

    /* ============================================================
       GET /reservas → Listar todas las reservas en JSON
    ============================================================ */
    public function index() {
        header('Content-Type: application/json');

        try {
            echo json_encode($this->model->all());
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /* ============================================================
       GET /reservas/show?id=XX
    ============================================================ */
    public function show() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "ID requerido"]);
            return;
        }

        header('Content-Type: application/json');

        try {
            $reserva = $this->model->find($id);
            echo json_encode($reserva);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }


    /* ============================================================
       POST /reservas  → Crear nueva reserva
    ============================================================ */
    public function store() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Método no permitido"]);
            return;
        }

        // Recibir datos
        $cliente_id   = $_POST['cliente_id'] ?? null;
        $servicio_id  = $_POST['servicio_id'] ?? null;
        $therapist_id = $_POST['therapist_id'] ?? null;
        $fecha        = $_POST['fecha'] ?? null;
        $hora         = $_POST['hora'] ?? null;
        $duracion_min = $_POST['duracion_min'] ?? null;
        $notas        = $_POST['notas'] ?? '';

        // Validación
        if (!$cliente_id || !$servicio_id || !$fecha || !$hora) {
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Faltan campos obligatorios'
            ]);
            return;
        }

        // Normalizar payload que espera el modelo
        $payload = [
            'cliente_id'   => (int)$cliente_id,
            'servicio_id'  => (int)$servicio_id,
            'therapist_id' => $therapist_id !== '' ? (int)$therapist_id : null,
            'fecha'        => $fecha,
            'hora'         => $hora,
            'duracion_min' => (int)($duracion_min ?: 60),
            'estado'       => 'Pendiente',
            'notas'        => trim($notas)
        ];

        try {
            $id = $this->model->create($payload);

            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'ok',
                'id' => $id
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


    /* ============================================================
       POST /reservas/update
    ============================================================ */
    public function update() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Método no permitido"]);
            return;
        }

        if (!isset($_POST['id'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "ID requerido"]);
            return;
        }

        $id = $_POST['id'];

        try {
            $this->model->update($id, $_POST);

            echo json_encode(["status" => "ok"]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }


    /* ============================================================
       POST /reservas/delete
    ============================================================ */
    public function delete() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Método no permitido"]);
            return;
        }

        if (!isset($_POST['id'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "ID requerido"]);
            return;
        }

        try {
            $this->model->delete($_POST['id']);
            echo json_encode(["status" => "ok"]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}
