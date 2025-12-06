<?php
require_once __DIR__ . '/../Config/Database.php';

class HistoryModel {

    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Obtener historial (reservas con JOIN)
     */
    public function getHistorial() {
        try {
            $sql = "
                SELECT 
                    r.id,
                    r.fecha,
                    r.hora,
                    c.nombre AS cliente,
                    s.nombre AS servicio,
                    t.nombre AS terapeuta,
                    r.estado,
                    r.created_at AS registrado_en
                FROM reservas r
                LEFT JOIN clientes c      ON c.id = r.cliente_id
                LEFT JOIN servicios s     ON s.id = r.servicio_id
                LEFT JOIN terapeutas t    ON t.id = r.therapist_id
                ORDER BY r.created_at DESC
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return [];
        }
    }
}
