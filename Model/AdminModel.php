<?php
require_once __DIR__ . '/../app/config.php';

class AdminModel {
    private PDO $db;
    
    public function __construct() {
        $this->db = DB::get();
    }
    
    public function getDashboardStats() {
        $stats = [];
        
        try {
            // 1. Total reservas de HOY
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM reservations WHERE fecha = CURDATE()");
            $stmt->execute();
            $stats['reservas_hoy'] = $stmt->fetch()['total'] ?? 0;
            
            // 2. Reservas CONFIRMADAS
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM reservations WHERE estado = 'Confirmada'");
            $stmt->execute();
            $stats['reservas_confirmadas'] = $stmt->fetch()['total'] ?? 0;
            
            // 3. Reservas PENDIENTES
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM reservations WHERE estado = 'Pendiente'");
            $stmt->execute();
            $stats['reservas_pendientes'] = $stmt->fetch()['total'] ?? 0;
            
            // 4. Total CLIENTES
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'client'");
            $stats['total_clientes'] = $stmt->fetch()['total'] ?? 0;
            
            // 5. Reservas de HOY con detalles - versión simple primero
            $stmt = $this->db->prepare("SELECT * FROM reservations WHERE fecha = CURDATE() ORDER BY hora ASC");
            $stmt->execute();
            $stats['reservas_hoy_detalle'] = $stmt->fetchAll();
            
        } catch (PDOException $e) {
            // En caso de error, devolver valores por defecto
            error_log("Error en AdminModel: " . $e->getMessage());
            $stats = [
                'reservas_hoy' => 0,
                'reservas_confirmadas' => 0,
                'reservas_pendientes' => 0,
                'total_clientes' => 0,
                'reservas_hoy_detalle' => []
            ];
        }
        
        return $stats;
    }
public function getAllReservations() {
    try {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                   u.nombre as cliente_nombre, 
                   s.nombre as servicio_nombre,
                   t.nombre as terapeuta_nombre
            FROM reservations r
            JOIN users u ON r.clients_id = u.id
            JOIN services s ON r.service_id = s.id
            LEFT JOIN therapists t ON r.therapist_id = t.id
            ORDER BY r.fecha DESC, r.hora DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error en getAllReservations: " . $e->getMessage());
        return [];
    }
}
}
