<?php
require_once __DIR__ . '/../app/config.php';

class HistoryModel {
    private $db;

    public function __construct($db = null) {
        $this->db = $db ?? DB::get();
    }

    /* ============================================================
       REGISTRO DE HISTORIAL DETALLADO
    ============================================================ */
    public function log($evento, $reservation_id = null, $user_id = null, $detalle = null, 
                        $tipo_evento = 'reserva', $anterior_valor = null, $nuevo_valor = null) {
        
        // Obtener información del usuario actual desde sesión
        $current_user_id = null;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user_id'])) {
            $current_user_id = $_SESSION['user_id'];
        }
        
        // Usar el usuario de sesión si no se especificó uno
        $user_id_final = $user_id ?? $current_user_id;
        
        // Obtener IP y user agent
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $sql = "INSERT INTO history 
                (reservation_id, user_id, evento, tipo_evento, detalle, 
                 anterior_valor, nuevo_valor, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $reservation_id,
            $user_id_final,
            $evento,
            $tipo_evento,
            $detalle,
            $anterior_valor ? json_encode($anterior_valor) : null,
            $nuevo_valor ? json_encode($nuevo_valor) : null,
            $ip_address,
            $user_agent
        ]);
    }

    /* ============================================================
       OBTENER TODO EL HISTORIAL CON PAGINACIÓN
    ============================================================ */
    public function getAll($limit = 50, $offset = 0, $filters = []) {
        $where = [];
        $params = [];
        
        // Aplicar filtros
        if (!empty($filters['tipo_evento']) && $filters['tipo_evento'] != 'todos') {
            $where[] = "h.tipo_evento = ?";
            $params[] = $filters['tipo_evento'];
        }
        
        if (!empty($filters['evento']) && $filters['evento'] != 'todos') {
            $where[] = "h.evento = ?";
            $params[] = $filters['evento'];
        }
        
        if (!empty($filters['fecha_desde'])) {
            $where[] = "DATE(h.created_at) >= ?";
            $params[] = $filters['fecha_desde'];
        }
        
        if (!empty($filters['fecha_hasta'])) {
            $where[] = "DATE(h.created_at) <= ?";
            $params[] = $filters['fecha_hasta'];
        }
        
        if (!empty($filters['busqueda'])) {
            $where[] = "(h.evento LIKE ? OR h.detalle LIKE ? OR u.nombre LIKE ? OR u.email LIKE ? OR h.ip_address LIKE ?)";
            $searchTerm = "%{$filters['busqueda']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        // IMPORTANTE: Cambié las columnas para que coincidan con tu tabla
        // Tu tabla tiene 'evento' (no 'evento'), y 'detalle' (no 'detalle')
        $sql = "SELECT 
                    h.*,
                    u.nombre AS usuario_nombre,
                    u.email AS usuario_email,
                    r.id AS reserva_id,
                    r.fecha AS reserva_fecha,
                    r.hora AS reserva_hora,
                    s.nombre AS servicio_nombre
                FROM history h
                LEFT JOIN users u ON h.user_id = u.id
                LEFT JOIN reservations r ON h.reservation_id = r.id
                LEFT JOIN services s ON r.servicio_id = s.id
                {$whereClause}
                ORDER BY h.created_at DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        error_log("SQL Historail: " . $sql);
        error_log("Params: " . json_encode($params));
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Historial encontrados: " . count($historial));
        
        // Procesar valores JSON
        foreach ($historial as &$item) {
            if ($item['anterior_valor']) {
                try {
                    $item['anterior_valor'] = json_decode($item['anterior_valor'], true);
                } catch (Exception $e) {
                    $item['anterior_valor'] = null;
                }
            }
            if ($item['nuevo_valor']) {
                try {
                    $item['nuevo_valor'] = json_decode($item['nuevo_valor'], true);
                } catch (Exception $e) {
                    $item['nuevo_valor'] = null;
                }
            }
        }
        
        // Obtener total para paginación
        $countSql = "SELECT COUNT(*) as total FROM history h 
                    LEFT JOIN users u ON h.user_id = u.id
                    LEFT JOIN reservations r ON h.reservation_id = r.id
                    {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute(array_slice($params, 0, -2)); // Remover limit y offset
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        return [
            'historial' => $historial,
            'total' => $total,
            'pagina_actual' => ($offset / $limit) + 1,
            'total_paginas' => ceil($total / $limit)
        ];
    }

    /* ============================================================
       OBTENER EVENTO POR ID
    ============================================================ */
    public function getById($id) {
        $sql = "SELECT 
                    h.*,
                    u.nombre as usuario_nombre,
                    u.email as usuario_email,
                    r.id as reserva_id,
                    r.fecha as reserva_fecha,
                    r.hora as reserva_hora,
                    s.nombre as servicio_nombre
                FROM history h
                LEFT JOIN users u ON h.user_id = u.id
                LEFT JOIN reservations r ON h.reservation_id = r.id
                LEFT JOIN services s ON r.servicio_id = s.id
                WHERE h.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        $evento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($evento) {
            // Procesar valores JSON
            if ($evento['anterior_valor']) {
                try {
                    $evento['anterior_valor'] = json_decode($evento['anterior_valor'], true);
                } catch (Exception $e) {
                    $evento['anterior_valor'] = null;
                }
            }
            if ($evento['nuevo_valor']) {
                try {
                    $evento['nuevo_valor'] = json_decode($evento['nuevo_valor'], true);
                } catch (Exception $e) {
                    $evento['nuevo_valor'] = null;
                }
            }
        }
        
        return $evento;
    }

    /* ============================================================
       REGISTRAR LOGIN/LOGOUT
    ============================================================ */
    public function logAuth($user_id, $evento, $detalle = null) {
        return $this->log($evento, null, $user_id, $detalle, 'usuario');
    }

    /* ============================================================
       REGISTRAR CAMBIOS EN USUARIOS
    ============================================================ */
    public function logUserChange($user_id, $evento, $anterior_valor = null, $nuevo_valor = null) {
        return $this->log($evento, null, $user_id, "Cambio en usuario ID: {$user_id}", 
                         'usuario', $anterior_valor, $nuevo_valor);
    }

    /* ============================================================
       MÉTODO PARA DEPURACIÓN: OBTENER ESTRUCTURA DE LA TABLA
    ============================================================ */
    public function getTableStructure() {
        $sql = "DESCRIBE history";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       VERIFICAR SI HAY DATOS EN LA TABLA
    ============================================================ */
    public function hasData() {
        $sql = "SELECT COUNT(*) as total FROM history";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }
}
