<?php

class ReservationModel {
    private $db;

    public function __construct($db = null) {
        $this->db = $db ?? DB::get();
    }

    /* ============================================================
       OBTENER TODAS LAS RESERVAS
    ============================================================ */
    public function all() {
        $sql = "SELECT r.*, 
                       c.nombre AS cliente, 
                       s.nombre AS servicio, 
                       t.nombre AS terapeuta
                FROM reservations r
                LEFT JOIN users c ON r.cliente_id = c.id
                LEFT JOIN services s ON r.servicio_id = s.id
                LEFT JOIN users t ON r.therapist_id = t.id
                ORDER BY r.fecha, r.hora";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       BUSCAR UNA RESERVA POR ID
    ============================================================ */
    public function find($id) {
        $sql = "SELECT * FROM reservations WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       CREAR UNA RESERVA
    ============================================================ */
    public function create($data) {
        $sql = "INSERT INTO reservations 
                (cliente_id, servicio_id, therapist_id, fecha, hora, duracion_min, estado, notas)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $data['cliente_id'],
            $data['servicio_id'],
            $data['therapist_id'],
            $data['fecha'],
            $data['hora'],
            $data['duracion_min'],
            $data['estado'],
            $data['notas']
        ]);

        return $this->db->lastInsertId();
    }

    /* ============================================================
       ACTUALIZAR RESERVA
    ============================================================ */
    public function update($id, $data) {
        $sql = "UPDATE reservations SET 
                    cliente_id   = ?,
                    servicio_id  = ?,
                    therapist_id = ?,
                    fecha        = ?,
                    hora         = ?,
                    duracion_min = ?,
                    estado       = ?,
                    notas        = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['cliente_id'],
            $data['servicio_id'],
            $data['therapist_id'] !== '' ? $data['therapist_id'] : null,
            $data['fecha'],
            $data['hora'],
            $data['duracion_min'],
            $data['estado'],
            trim($data['notas']),
            $id
        ]);
    }

    /* ============================================================
       ELIMINAR RESERVA
    ============================================================ */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM reservations WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /* ============================================================
       RESERVAS PASADAS SIN REGISTRO EN HISTORIAL
    ============================================================ */
    public function getPastReservationsWithoutHistory() {
        $sql = "SELECT r.*
                FROM reservations r
                LEFT JOIN history h ON h.reservation_id = r.id
                WHERE r.fecha < CURDATE()
                AND h.id IS NULL
                AND r.estado != 'completada'";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       MARCAR RESERVA COMO COMPLETADA
    ============================================================ */
    public function markAsCompleted($id) {
        $sql = "UPDATE reservations SET estado = 'completada' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
