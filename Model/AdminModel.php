<?php
require_once __DIR__ . '/../app/config.php';

class AdminModel {
    private PDO $db;

    public function __construct() {
        $this->db = DB::get();
    }

    /**
     * Devuelve estadísticas para el dashboard:
     * - reservas_hoy (int)
     * - reservas_confirmadas (int)
     * - reservas_pendientes (int)
     * - total_clientes (int)
     * - reservas_hoy_detalle (array) -> each item has cliente_nombre, servicio_nombre, terapeuta_nombre, estado, estado_display, etc.
     */
   
public function getDashboardStats(): array {
    $stats = [
        'reservas_hoy' => 0,
        'reservas_confirmadas' => 0,
        'reservas_pendientes' => 0,
        'total_clientes' => 0,
        'reservas_hoy_detalle' => []
    ];

    try {
        // 1) Total reservas de HOY
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reservations WHERE fecha = CURDATE()");
        $stmt->execute();
        $stats['reservas_hoy'] = (int) ($stmt->fetchColumn() ?: 0);

        // 2) Reservas CONFIRMADAS
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reservations WHERE LOWER(estado) = 'confirmada'");
        $stmt->execute();
        $stats['reservas_confirmadas'] = (int) ($stmt->fetchColumn() ?: 0);

        // 3) Reservas PENDIENTES
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reservations WHERE LOWER(estado) = 'pendiente'");
        $stmt->execute();
        $stats['reservas_pendientes'] = (int) ($stmt->fetchColumn() ?: 0);

        // 4) Total CLIENTES
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE role = 'client'");
        $stmt->execute();
        $stats['total_clientes'] = (int) ($stmt->fetchColumn() ?: 0);

        // 5) Reservas de HOY con detalles (VERSIÓN FINAL)
        $sql = "
            SELECT
                r.id,
                r.cliente_id,
                r.servicio_id,
                r.therapist_id,
                r.fecha,
                r.hora,
                r.duracion_min,
                r.notas,
                r.estado,

                u.nombre AS cliente_nombre,
                s.nombre AS servicio_nombre,
                t.nombre AS terapeuta_nombre,

                CONCAT(UPPER(LEFT(r.estado,1)), SUBSTRING(r.estado,2)) AS estado_display
            FROM reservations r
            LEFT JOIN users u      ON r.cliente_id = u.id
            LEFT JOIN services s   ON r.servicio_id = s.id
            LEFT JOIN therapists t ON r.therapist_id = t.id
            WHERE r.fecha = CURDATE()
            ORDER BY r.hora ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['reservas_hoy_detalle'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error en AdminModel::getDashboardStats -> " . $e->getMessage());
    }

    return $stats;
}
    /**
     * Obtener todas las reservas (para módulo reservas).
     * Incluye nombres y alias correctos; usado por vistas de administración.
     */
    public function getAllReservations(): array {
        try {
            $sql = "
                SELECT r.*,
                       u.nombre AS cliente_nombre,
                       s.nombre AS servicio_nombre,
                       t.nombre AS terapeuta_nombre,
                       CONCAT(UPPER(LEFT(r.estado,1)), SUBSTRING(r.estado,2)) AS estado_display
                FROM reservations r
                LEFT JOIN users u ON r.cliente_id = u.id
                LEFT JOIN services s ON r.servicio_id = s.id
                LEFT JOIN therapists t ON r.therapist_id = t.id
                ORDER BY r.fecha DESC, r.hora DESC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en AdminModel::getAllReservations -> " . $e->getMessage());
            return [];
        }
    }
    
public function getHistorial(): array {
    try {
        $sql = "
            SELECT r.*,
                   u.nombre AS cliente_nombre,
                   s.nombre AS servicio_nombre,
                   t.nombre AS terapeuta_nombre,
                   CONCAT(UPPER(LEFT(r.estado,1)), SUBSTRING(r.estado,2)) AS estado_display
            FROM reservations r
            LEFT JOIN users u ON r.cliente_id = u.id
            LEFT JOIN services s ON r.servicio_id = s.id
            LEFT JOIN therapists t ON r.therapist_id = t.id
            ORDER BY r.fecha DESC, r.hora DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error en AdminModel::getHistorial -> " . $e->getMessage());
        return [];
    }
}
}
