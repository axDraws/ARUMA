<?php
// Model/ReservationModel.php
require_once __DIR__ . '/../app/config.php';

class ReservationModel {
    private PDO $db;
    public function __construct() {
        $this->db = DB::get();
    }

    public function allToday(): array {
        $stmt = $this->db->prepare("SELECT r.*, u.nombre as cliente, s.nombre as servicio, t.nombre as terapeuta
            FROM reservations r
            LEFT JOIN users u ON r.cliente_id = u.id
            LEFT JOIN services s ON r.servicio_id = s.id
            LEFT JOIN therapists t ON r.therapist_id = t.id
            WHERE r.fecha = CURDATE()
            ORDER BY r.hora ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO reservations
            (cliente_id, servicio_id, therapist_id, fecha, hora, duracion_min, estado, notas)
            VALUES (:cliente, :servicio, :therapist, :fecha, :hora, :duracion, :estado, :notas)");
        $stmt->execute([
            ':cliente' => $data['cliente_id'],
            ':servicio' => $data['servicio_id'],
            ':therapist' => $data['therapist_id'] ?? null,
            ':fecha' => $data['fecha'],
            ':hora' => $data['hora'],
            ':duracion' => $data['duracion_min'] ?? null,
            ':estado' => $data['estado'] ?? 'pendiente',
            ':notas' => $data['notas'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    // Agrega más métodos: find($id), update($id,$data), delete($id)
}

