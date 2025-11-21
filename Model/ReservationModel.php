<?php
require_once __DIR__ . '/../app/config.php';

class ReservationModel {

    private PDO $db;

    public function __construct() {
        $this->db = DB::get();
    }

    // Obtener todas las reservas con JOINs
    public function all() {
        $sql = "
            SELECT r.*, 
                   u.nombre AS cliente_nombre,
                   s.nombre AS servicio_nombre,
                   t.nombre AS terapeuta_nombre
            FROM reservations r
            JOIN users u       ON r.cliente_id = u.id
            JOIN services s    ON r.servicio_id = s.id
            LEFT JOIN therapists t ON r.therapist_id = t.id
            ORDER BY r.fecha DESC, r.hora DESC;
        ";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Encontrar una reserva por ID
    public function find($id) {
        $stmt = $this->db->prepare("
            SELECT * FROM reservations WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

// Crear reserva (método robusto)
public function create(array $data) {
    // Normalizar valores y claves esperadas
    $params = [
        ':cliente_id'   => isset($data['cliente_id']) && $data['cliente_id'] !== '' ? (int)$data['cliente_id'] : null,
        ':servicio_id'  => isset($data['servicio_id']) && $data['servicio_id'] !== '' ? (int)$data['servicio_id'] : null,
        ':therapist_id' => isset($data['therapist_id']) && $data['therapist_id'] !== '' ? (int)$data['therapist_id'] : null,
        ':fecha'        => $data['fecha'] ?? null,
        ':hora'         => $data['hora'] ?? null,
        ':duracion_min' => isset($data['duracion_min']) && $data['duracion_min'] !== '' ? (int)$data['duracion_min'] : 0,
        ':estado'       => $data['estado'] ?? 'Pendiente',
        ':notas'        => $data['notas'] ?? ''
    ];

    // Si cliente_id o servicio_id son nulos, lanzar error (regla de negocio)
    if (empty($params[':cliente_id']) || empty($params[':servicio_id'])) {
        throw new InvalidArgumentException("cliente_id y servicio_id son obligatorios");
    }

    $sql = "
        INSERT INTO reservations 
            (cliente_id, servicio_id, therapist_id, fecha, hora, duracion_min, estado, notas)
        VALUES 
            (:cliente_id, :servicio_id, :therapist_id, :fecha, :hora, :duracion_min, :estado, :notas)
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return (int)$this->db->lastInsertId();
}

    // Actualizar reserva
    public function update($id, $data) {
        $data['id'] = $id;

        $stmt = $this->db->prepare("
            UPDATE reservations 
            SET servicio_id = :servicio_id,
                therapist_id = :therapist_id,
                fecha = :fecha,
                hora = :hora,
                duracion_min = :duracion_min,
                estado = :estado,
                notas = :notas
            WHERE id = :id
        ");
        return $stmt->execute($data);
    }

    // Eliminar reserva
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM reservations WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Cambiar estado
    public function updateEstado($id, $estado) {
        $stmt = $this->db->prepare("
            UPDATE reservations SET estado = :estado WHERE id = :id
        ");
        return $stmt->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }
}
