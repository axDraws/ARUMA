
<?php
require_once __DIR__ . '/../app/config.php';

class ReservationModel {
    private PDO $db;
    
    public function __construct() {
        $this->db = DB::get();
    }
    
    /* ============================================================
       OBTENER TODAS LAS RESERVAS
    ============================================================ */
    public function all() {
        $sql = "
            SELECT r.*, 
                   u.nombre       AS cliente_nombre,
                   s.nombre       AS servicio_nombre,
                   t.nombre       AS terapeuta_nombre
            FROM reservations r
            JOIN users u        ON r.cliente_id = u.id
            JOIN services s     ON r.servicio_id = s.id
            LEFT JOIN therapists t ON r.therapist_id = t.id
            ORDER BY r.fecha DESC, r.hora DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /* ============================================================
       BUSCAR RESERVA POR ID
    ============================================================ */
    public function find($id) {
        $sql = "
            SELECT r.*, 
                   u.nombre       AS cliente_nombre,
                   s.nombre       AS servicio_nombre,
                   t.nombre       AS terapeuta_nombre
            FROM reservations r
            JOIN users u        ON r.cliente_id = u.id
            JOIN services s     ON r.servicio_id = s.id
            LEFT JOIN therapists t ON r.therapist_id = t.id
            WHERE r.id = :id
            LIMIT 1
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /* ============================================================
       CREAR NUEVA RESERVA
    ============================================================ */
    public function create(array $data) {
        $sql = "
            INSERT INTO reservations 
            (cliente_id, servicio_id, therapist_id, fecha, hora, duracion_min, estado, notas, created_at)
            VALUES (:cliente_id, :servicio_id, :therapist_id, :fecha, :hora, :duracion_min, :estado, :notas, NOW())
        ";
        
        $params = [
            ':cliente_id'    => $data['cliente_id'] ?? $data['clients_id'] ?? null,
            ':servicio_id'   => $data['servicio_id'] ?? $data['service_id'] ?? null,
            ':therapist_id'  => !empty($data['therapist_id']) ? $data['therapist_id'] : null,
            ':fecha'         => $data['fecha'] ?? null,
            ':hora'          => $data['hora'] ?? null,
            ':duracion_min'  => isset($data['duracion_min']) ? (int)$data['duracion_min'] : null,
            ':estado'        => isset($data['estado']) ? mb_strtolower($data['estado']) : 'pendiente',
            ':notas'         => $data['notas'] ?? null
        ];
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return (int)$this->db->lastInsertId();
    }
    
    /* ============================================================
       ACTUALIZAR RESERVA
    ============================================================ */
    public function update($id, $data) {
        $sql = "
            UPDATE reservations 
            SET cliente_id = :cliente_id,
                servicio_id = :servicio_id,
                therapist_id = :therapist_id,
                fecha = :fecha,
                hora = :hora,
                duracion_min = :duracion_min,
                estado = :estado,
                notas = :notas
            WHERE id = :id
        ";
        
        $params = [
            ':cliente_id'   => $data['cliente_id'] ?? $data['clients_id'] ?? null,
            ':servicio_id'  => $data['servicio_id'] ?? $data['service_id'] ?? null,
            ':therapist_id' => array_key_exists('therapist_id', $data) && $data['therapist_id'] !== '' ? $data['therapist_id'] : null,
            ':fecha'        => $data['fecha'] ?? null,
            ':hora'         => $data['hora'] ?? null,
            ':duracion_min' => isset($data['duracion_min']) ? (int)$data['duracion_min'] : null,
            ':estado'       => isset($data['estado']) ? mb_strtolower($data['estado']) : 'pendiente',
            ':notas'        => $data['notas'] ?? null,
            ':id'           => $id
        ];
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /* ============================================================
       ELIMINAR
    ============================================================ */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM reservations WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    /* ============================================================
       CAMBIAR ESTADO
    ============================================================ */
    public function updateEstado($id, $estado) {
        $stmt = $this->db->prepare("
            UPDATE reservations SET estado = :estado WHERE id = :id
        ");
        
        return $stmt->execute([
            ':estado' => mb_strtolower($estado),
            ':id'     => $id
        ]);
    }
    
    /* ============================================================
       MÉTODO PARA OBTENER RESERVAS DE HOY (Para el dashboard)
    ============================================================ */
    public function getTodayReservations() {
        $sql = "
            SELECT r.*, 
                   u.nombre as cliente_nombre, 
                   s.nombre as servicio_nombre,
                   t.nombre as terapeuta_nombre
            FROM reservations r
            JOIN users u ON r.cliente_id = u.id
            JOIN services s ON r.servicio_id = s.id
            LEFT JOIN therapists t ON r.therapist_id = t.id
            WHERE r.fecha = CURDATE()
            ORDER BY r.hora ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
