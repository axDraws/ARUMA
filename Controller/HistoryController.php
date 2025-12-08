<?php
require_once __DIR__ . '/../Model/ReservationModel.php';
require_once __DIR__ . '/../Model/HistoryModel.php';
require_once __DIR__ . '/../Model/ClientModel.php';
require_once __DIR__ . '/../Model/ServiceModel.php';
require_once __DIR__ . '/../Model/TherapistModel.php';

class HistoryController {
    private $reservationModel;
    private $historyModel;

    public function __construct() {
        $this->reservationModel = new ReservationModel();
        $this->historyModel = new HistoryModel();
    }

    /* ============================================================
       PÁGINA PRINCIPAL DE HISTORIAL
    ============================================================ */
    public function index() {
        // Verificar que el usuario sea administrador
        $this->checkAdmin();
        
        // Procesar reservas pasadas automáticamente
        $this->processHistoryLogic();
        
        // Obtener parámetros de filtro
        $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
        $limit = 20;
        $offset = ($pagina - 1) * $limit;
        
        $filters = [
            'tipo_evento' => $_GET['tipo'] ?? 'todos',
            'evento' => $_GET['evento'] ?? 'todos',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'busqueda' => $_GET['busqueda'] ?? ''
        ];
        
        // Obtener historial con filtros
        $data = $this->historyModel->getAll($limit, $offset, $filters);
        
        // Obtener otras variables necesarias para la vista
        $clientModel = new ClientModel();
        $serviceModel = new ServiceModel();
        $therapistModel = new TherapistModel();
        
        // Variables que deben estar disponibles en administrador.php
        $historial = $data['historial'] ?? [];
        $total = $data['total'] ?? 0;
        $pagina_actual = $data['pagina_actual'] ?? 1;
        $total_paginas = $data['total_paginas'] ?? 1;
        $clientes = $clientModel->getAllClients();
        $servicios = $serviceModel->getAllServices();
        $terapeutas = $therapistModel->getAllTherapists();
        
        // Para el dashboard
        require_once __DIR__ . '/../Model/AdminModel.php';
        $adminModel = new AdminModel();
        $stats = $adminModel->getDashboardStats();
        
        $reservas_hoy = $stats['reservas_hoy'] ?? 0;
        $reservas_confirmadas = $stats['reservas_confirmadas'] ?? 0;
        $reservas_pendientes = $stats['reservas_pendientes'] ?? 0;
        $total_clientes = $stats['total_clientes'] ?? 0;
        $reservas_detalle = $stats['reservas_hoy_detalle'] ?? [];
        
        // Para la sección de reservas
        $reservas = $this->reservationModel->all();
        
        // Cargar vista con todas las variables necesarias
        require_once __DIR__ . '/../views/administrador.php';
    }

    /* ============================================================
       API: OBTENER HISTORIAL (JSON) - para AJAX
    ============================================================ */
    public function getHistorialApi() {
        $this->checkAdmin();
        
        // Obtener parámetros
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
        $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
        $offset = ($pagina - 1) * $limit;
        
        // Filtros
        $filters = [
            'tipo_evento' => $_GET['tipo'] ?? 'todos',
            'evento' => $_GET['evento'] ?? 'todos',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'busqueda' => $_GET['busqueda'] ?? ''
        ];
        
        // Obtener datos del modelo
        $data = $this->historyModel->getAll($limit, $offset, $filters);
        
        // Devolver JSON
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'historial' => $data['historial'] ?? [],
            'total' => $data['total'] ?? 0,
            'pagina_actual' => $data['pagina_actual'] ?? 1,
            'total_paginas' => $data['total_paginas'] ?? 1
        ]);
        exit();
    }

    /* ============================================================
       API: OBTENER ESTADÍSTICAS
    ============================================================ */
    public function getEstadisticasApi() {
        $this->checkAdmin();
        
        // Obtener total de registros
        $sql = "SELECT COUNT(*) as total FROM history";
        $stmt = DB::get()->prepare($sql);
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Registros de hoy
        $hoy = date('Y-m-d');
        $sql = "SELECT COUNT(*) as hoy FROM history WHERE DATE(created_at) = ?";
        $stmt = DB::get()->prepare($sql);
        $stmt->execute([$hoy]);
        $hoyCount = $stmt->fetch(PDO::FETCH_ASSOC)['hoy'] ?? 0;
        
        // Últimos 7 días
        $sql = "SELECT COUNT(*) as ultimos7 FROM history WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt = DB::get()->prepare($sql);
        $stmt->execute();
        $ultimos7 = $stmt->fetch(PDO::FETCH_ASSOC)['ultimos7'] ?? 0;
        
        // Usuarios activos (con actividad en los últimos 7 días)
        $sql = "SELECT COUNT(DISTINCT user_id) as usuarios FROM history 
                WHERE user_id IS NOT NULL 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt = DB::get()->prepare($sql);
        $stmt->execute();
        $usuariosActivos = $stmt->fetch(PDO::FETCH_ASSOC)['usuarios'] ?? 0;
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'total' => $total,
            'hoy' => $hoyCount,
            'ultimos7Dias' => $ultimos7,
            'usuariosActivos' => $usuariosActivos
        ]);
        exit();
    }

    /* ============================================================
       API: OBTENER EVENTO POR ID
    ============================================================ */
    public function getEventoByIdApi() {
        $this->checkAdmin();
        
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
            exit();
        }
        
        $id = intval($_GET['id']);
        $evento = $this->historyModel->getById($id);
        
        if (!$evento) {
            http_response_code(404);
            echo json_encode(['error' => 'Evento no encontrado']);
            exit();
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'evento' => $evento
        ]);
        exit();
    }

    /* ============================================================
       API: LIMPIAR HISTORIAL
    ============================================================ */
    public function limpiarHistorialApi() {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit();
        }
        
        // Obtener días desde POST
        $dias = isset($_POST['dias']) ? intval($_POST['dias']) : 90;
        $dias = max(1, min($dias, 365)); // Entre 1 y 365 días
        
        // Eliminar registros antiguos
        $sql = "DELETE FROM history WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        $stmt = DB::get()->prepare($sql);
        $stmt->execute([$dias]);
        $eliminados = $stmt->rowCount();
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'mensaje' => "Historial limpiado correctamente",
            'eliminados' => $eliminados,
            'dias' => $dias
        ]);
        exit();
    }

    /* ============================================================
       API: EXPORTAR HISTORIAL (CSV)
    ============================================================ */
    public function exportarHistorialApi() {
        $this->checkAdmin();
        
        // Obtener todos los registros sin paginación
        $sql = "SELECT 
                    h.id,
                    h.created_at,
                    u.nombre as usuario,
                    h.evento,
                    h.tipo_evento,
                    h.detalle,
                    h.ip_address,
                    h.user_agent
                FROM history h
                LEFT JOIN users u ON h.user_id = u.id
                ORDER BY h.created_at DESC";
        
        $stmt = DB::get()->prepare($sql);
        $stmt->execute();
        $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Configurar headers para descarga CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=historial_' . date('Y-m-d') . '.csv');
        
        // Crear archivo CSV
        $output = fopen('php://output', 'w');
        
        // Encabezados
        fputcsv($output, [
            'ID', 'Fecha/Hora', 'Usuario', 'Tipo', 'Evento', 
            'Detalles', 'Dirección IP', 'Navegador'
        ]);
        
        // Datos
        foreach ($historial as $row) {
            fputcsv($output, [
                $row['id'],
                $row['created_at'],
                $row['usuario'] ?? 'Sistema',
                $row['tipo_evento'],
                $row['evento'],
                $row['detalle'] ?? '',
                $row['ip_address'] ?? '',
                $row['user_agent'] ?? ''
            ]);
        }
        
        fclose($output);
        exit();
    }

    /* ============================================================
       PROCESAR LÓGICA DE HISTORIAL AUTOMÁTICO
    ============================================================ */
    public function processHistoryLogic() {
        // 1. Obtener reservas pasadas sin historial de auto-completado
        $pasadas = $this->reservationModel->getPastReservationsWithoutHistory();

        foreach ($pasadas as $r) {
            // 2. Actualizar el estado a completada
            $this->reservationModel->markAsCompleted($r['id']);

            // 3. Detalle para el historial
            $detalle = "Reserva del {$r['fecha']} a las {$r['hora']} fue marcada como completada automáticamente.";

            // 4. Registrar historial
            $this->historyModel->log(
                "Auto-completada",      // $evento
                $r['id'],               // $reservation_id
                $r['cliente_id'] ?? null, // $user_id
                $detalle,               // $detalle
                'reserva'               // $tipo_evento
            );
        }
    }

    /* ============================================================
       VERIFICAR SI ES ADMIN
    ============================================================ */
 private function checkAdmin() {
    // SOLUCIÓN: Verificar si la sesión ya está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        if ($this->isAjaxRequest()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        } else {
            header('Location: /');
            exit();
        }
    }
}
   /* ============================================================
       VERIFICAR SI ES PETICIÓN AJAX
    ============================================================ */
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
