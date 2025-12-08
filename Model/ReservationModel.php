<?php
// Model/ReservationModel.php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/HistoryModel.php';

class ReservationModel {
    private PDO $db;
    private HistoryModel $historyModel;
    
    public function __construct() {
        $this->db = DB::get();
        $this->historyModel = new HistoryModel();
    }
    
    /* ============================================================
       OBTENER TODAS LAS RESERVAS - MÉTODO all() COMPLETO
    ============================================================ */
    public function all() {
        try {
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
            
            // Verificar si hay error
            if ($stmt->errorCode() !== '00000') {
                $error = $stmt->errorInfo();
                error_log("Error SQL en all(): " . print_r($error, true));
                return [];
            }
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Asegurar que siempre devolvemos un array
            return is_array($result) ? $result : [];
            
        } catch (Exception $e) {
            error_log("Exception en ReservationModel::all(): " . $e->getMessage());
            return [];
        }
    }
    
    /* ============================================================
       BUSCAR RESERVA POR ID
    ============================================================ */
    public function find($id) {
        try {
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
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            
        } catch (Exception $e) {
            error_log("Exception en ReservationModel::find(): " . $e->getMessage());
            return null;
        }
    }
    
    /* ============================================================
       CREAR NUEVA RESERVA
    ============================================================ */
    public function create(array $data) {
        try {
            $sql = "
                INSERT INTO reservations 
                (cliente_id, servicio_id, therapist_id, fecha, hora, duracion_min, estado, notas, created_at)
                VALUES (:cliente_id, :servicio_id, :therapist_id, :fecha, :hora, :duracion_min, :estado, :notas, NOW())
            ";
            
            $params = [
                ':cliente_id'    => $data['cliente_id'] ?? null,
                ':servicio_id'   => $data['servicio_id'] ?? null,
                ':therapist_id'  => !empty($data['therapist_id']) ? $data['therapist_id'] : null,
                ':fecha'         => $data['fecha'] ?? null,
                ':hora'          => $data['hora'] ?? null,
                ':duracion_min'  => isset($data['duracion_min']) ? (int)$data['duracion_min'] : null,
                ':estado'        => isset($data['estado']) ? mb_strtolower($data['estado']) : 'pendiente',
                ':notas'         => $data['notas'] ?? null
            ];
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            $reservaId = (int)$this->db->lastInsertId();
            
            // Registrar en historial
            $this->registrarCreacionReserva($reservaId, $data);
            
            return $reservaId;
            
        } catch (Exception $e) {
            error_log("Exception en ReservationModel::create(): " . $e->getMessage());
            return false;
        }
    }
    
    /* ============================================================
       ACTUALIZAR RESERVA
    ============================================================ */
    public function update($id, $data, $anterior = null) {
        try {
            // Obtener datos anteriores si no se proporcionan
            if (!$anterior) {
                $anterior = $this->find($id);
            }
            
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
                ':cliente_id'   => $data['cliente_id'] ?? $anterior['cliente_id'] ?? null,
                ':servicio_id'  => $data['servicio_id'] ?? $anterior['servicio_id'] ?? null,
                ':therapist_id' => isset($data['therapist_id']) && $data['therapist_id'] !== '' ? $data['therapist_id'] : null,
                ':fecha'        => $data['fecha'] ?? $anterior['fecha'] ?? null,
                ':hora'         => $data['hora'] ?? $anterior['hora'] ?? null,
                ':duracion_min' => isset($data['duracion_min']) ? (int)$data['duracion_min'] : ($anterior['duracion_min'] ?? null),
                ':estado'       => isset($data['estado']) ? mb_strtolower($data['estado']) : ($anterior['estado'] ?? 'pendiente'),
                ':notas'        => $data['notas'] ?? $anterior['notas'] ?? null,
                ':id'           => $id
            ];
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute($params);
            
            // Registrar en historial si fue exitoso
            if ($resultado && $anterior) {
                $this->registrarActualizacionReserva($id, $anterior, $data);
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Exception en ReservationModel::update(): " . $e->getMessage());
            return false;
        }
    }
    
    /* ============================================================
       ELIMINAR RESERVA
    ============================================================ */
    public function delete($id) {
        try {
            // Obtener datos antes de eliminar
            $reserva = $this->find($id);
            
            if (!$reserva) {
                return false;
            }
            
            $stmt = $this->db->prepare("DELETE FROM reservations WHERE id = :id");
            $resultado = $stmt->execute([':id' => $id]);
            
            // Registrar en historial si fue exitoso
            if ($resultado) {
                $this->registrarEliminacionReserva($reserva);
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Exception en ReservationModel::delete(): " . $e->getMessage());
            return false;
        }
    }
    
    /* ============================================================
       MÉTODOS PARA EL HISTORIAL (PRIVADOS)
    ============================================================ */
    
    private function registrarCreacionReserva($reservaId, $data) {
        try {
            $detalle = "Nueva reserva creada";
            
            if (!empty($data['cliente_nombre'])) {
                $detalle .= " para cliente: " . $data['cliente_nombre'];
            }
            
            if (!empty($data['servicio_nombre'])) {
                $detalle .= " | Servicio: " . $data['servicio_nombre'];
            }
            
            $detalle .= " | Fecha: " . ($data['fecha'] ?? 'N/A');
            $detalle .= " | Hora: " . ($data['hora'] ?? 'N/A');
            $detalle .= " | Estado: " . ($data['estado'] ?? 'pendiente');
            
            $this->historyModel->log(
                'Reserva Creada',
                $reservaId,
                $data['cliente_id'] ?? null,
                $detalle,
                'reserva',
                null,
                $data
            );
        } catch (Exception $e) {
            error_log("Error al registrar creación de reserva: " . $e->getMessage());
        }
    }
    
    private function registrarActualizacionReserva($reservaId, $reservaAnterior, $datosNuevos) {
        try {
            $cambios = [];
            
            $estadoAnterior = mb_strtolower($reservaAnterior['estado'] ?? 'pendiente');
            $estadoNuevo = mb_strtolower($datosNuevos['estado'] ?? $estadoAnterior);
            
            if ($estadoAnterior !== $estadoNuevo) {
                $cambios[] = "Estado: " . ucfirst($estadoAnterior) . " → " . ucfirst($estadoNuevo);
            }
            
            $fechaHoraAnterior = ($reservaAnterior['fecha'] ?? '') . ' ' . ($reservaAnterior['hora'] ?? '');
            $fechaHoraNuevo = ($datosNuevos['fecha'] ?? $reservaAnterior['fecha'] ?? '') . ' ' . 
                             ($datosNuevos['hora'] ?? $reservaAnterior['hora'] ?? '');
            
            if ($fechaHoraAnterior !== $fechaHoraNuevo) {
                $cambios[] = "Fecha/Hora: {$fechaHoraAnterior} → {$fechaHoraNuevo}";
            }
            
            $terapeutaAnteriorId = $reservaAnterior['therapist_id'] ?? null;
            $terapeutaNuevoId = $datosNuevos['therapist_id'] ?? $terapeutaAnteriorId;
            
            if ($terapeutaAnteriorId != $terapeutaNuevoId) {
                $cambios[] = "Terapeuta cambiado";
            }
            
            $notasAnterior = trim($reservaAnterior['notas'] ?? '');
            $notasNuevo = trim($datosNuevos['notas'] ?? $notasAnterior);
            
            if ($notasAnterior !== $notasNuevo) {
                $cambios[] = "Notas actualizadas";
            }
            
            if (!empty($cambios)) {
                $detalle = implode(' | ', $cambios);
                
                $this->historyModel->log(
                    'Reserva Actualizada',
                    $reservaId,
                    $reservaAnterior['cliente_id'] ?? null,
                    $detalle,
                    'reserva',
                    [
                        'estado' => $estadoAnterior,
                        'fecha' => $reservaAnterior['fecha'] ?? null,
                        'hora' => $reservaAnterior['hora'] ?? null,
                        'therapist_id' => $terapeutaAnteriorId,
                        'notas' => $notasAnterior
                    ],
                    [
                        'estado' => $estadoNuevo,
                        'fecha' => $datosNuevos['fecha'] ?? $reservaAnterior['fecha'],
                        'hora' => $datosNuevos['hora'] ?? $reservaAnterior['hora'],
                        'therapist_id' => $terapeutaNuevoId,
                        'notas' => $notasNuevo
                    ]
                );
            }
        } catch (Exception $e) {
            error_log("Error al registrar actualización de reserva: " . $e->getMessage());
        }
    }
    
    private function registrarEliminacionReserva($reserva) {
        try {
            $detalle = "Reserva eliminada del sistema";
            
            if (!empty($reserva['cliente_nombre'])) {
                $detalle .= " | Cliente: " . $reserva['cliente_nombre'];
            }
            
            if (!empty($reserva['servicio_nombre'])) {
                $detalle .= " | Servicio: " . $reserva['servicio_nombre'];
            }
            
            if (!empty($reserva['fecha'])) {
                $detalle .= " | Fecha: " . $reserva['fecha'];
            }
            
            $this->historyModel->log(
                'Reserva Eliminada',
                null,
                $reserva['cliente_id'] ?? null,
                $detalle,
                'reserva',
                [
                    'id' => $reserva['id'],
                    'cliente_id' => $reserva['cliente_id'] ?? null,
                    'servicio_id' => $reserva['servicio_id'] ?? null,
                    'fecha' => $reserva['fecha'] ?? null,
                    'hora' => $reserva['hora'] ?? null,
                    'estado' => $reserva['estado'] ?? null
                ],
                null
            );
        } catch (Exception $e) {
            error_log("Error al registrar eliminación de reserva: " . $e->getMessage());
        }
    }
    
    /* ============================================================
       MÉTODOS ADICIONALES
    ============================================================ */
    
    public function updateEstado($id, $estado) {
        try {
            $reserva = $this->find($id);
            $estadoAnterior = $reserva['estado'] ?? 'pendiente';
            
            $stmt = $this->db->prepare("
                UPDATE reservations SET estado = :estado WHERE id = :id
            ");
            
            $resultado = $stmt->execute([
                ':estado' => mb_strtolower($estado),
                ':id'     => $id
            ]);
            
            if ($resultado) {
                $this->registrarCambioEstado($id, $estadoAnterior, $estado);
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Exception en ReservationModel::updateEstado(): " . $e->getMessage());
            return false;
        }
    }
    
    private function registrarCambioEstado($reservaId, $estadoAnterior, $estadoNuevo) {
        try {
            $detalle = "Cambio de estado: " . ucfirst($estadoAnterior) . " → " . ucfirst($estadoNuevo);
            
            $this->historyModel->log(
                'Cambio de Estado',
                $reservaId,
                null,
                $detalle,
                'reserva',
                ['estado' => $estadoAnterior],
                ['estado' => $estadoNuevo]
            );
        } catch (Exception $e) {
            error_log("Error al registrar cambio de estado: " . $e->getMessage());
        }
    }
    
    public function getTodayReservations() {
        try {
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
            
        } catch (Exception $e) {
            error_log("Exception en ReservationModel::getTodayReservations(): " . $e->getMessage());
            return [];
        }
    }
    
    public function getPastReservationsWithoutHistory() {
        try {
            $sql = "
                SELECT r.*,
                       u.nombre as cliente_nombre,
                       s.nombre as servicio_nombre
                FROM reservations r
                JOIN users u ON r.cliente_id = u.id
                JOIN services s ON r.servicio_id = s.id
                LEFT JOIN history h ON h.reservation_id = r.id AND h.evento = 'Auto-completada'
                WHERE r.fecha < CURDATE()
                AND r.estado NOT IN ('completada', 'cancelada')
                AND h.id IS NULL
                ORDER BY r.fecha ASC
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Exception en ReservationModel::getPastReservationsWithoutHistory(): " . $e->getMessage());
            return [];
        }
    }
    
    public function markAsCompleted($id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE reservations SET estado = 'completada' WHERE id = :id
            ");
            return $stmt->execute([':id' => $id]);
            
        } catch (Exception $e) {
            error_log("Exception en ReservationModel::markAsCompleted(): " . $e->getMessage());
            return false;
        }
    }
    
    public function getReservationStats() {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN fecha = CURDATE() THEN 1 ELSE 0 END) as hoy,
                    SUM(CASE WHEN estado = 'confirmada' THEN 1 ELSE 0 END) as confirmadas,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END) as completadas,
                    SUM(CASE WHEN estado = 'cancelada' THEN 1 ELSE 0 END) as canceladas
                FROM reservations
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Exception en ReservationModel::getReservationStats(): " . $e->getMessage());
            return [
                'total' => 0,
                'hoy' => 0,
                'confirmadas' => 0,
                'pendientes' => 0,
                'completadas' => 0,
                'canceladas' => 0
            ];
        }
    }
}
