<?php
// Controller/ReservationController.php
require_once __DIR__ . '/../Model/ReservationModel.php';

class ReservationController {
    private ReservationModel $model;
    public function __construct() {
        $this->model = new ReservationModel();
    }

    public function index() {
        $reservas = $this->model->allToday();
        require __DIR__ . '/../views/reservations/index.php';
    }

    public function store() {
        // asume POST
        $data = [
            'cliente_id' => (int)$_POST['cliente_id'],
            'servicio_id' => (int)$_POST['servicio_id'],
            'therapist_id' => !empty($_POST['therapist_id']) ? (int)$_POST['therapist_id'] : null,
            'fecha' => $_POST['fecha'],
            'hora' => $_POST['hora'],
            'duracion_min' => (int)($_POST['duracion_min'] ?? 60),
            'notas' => $_POST['notas'] ?? null,
        ];
        $id = $this->model->create($data);
        header("Location: /?msg=created&id={$id}");
    }
}

