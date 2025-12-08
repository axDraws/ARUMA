<?php
// debug_historial.php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

require_once __DIR__ . '/Controller/HistoryController.php';

echo "<h2>Debug del Historial Controller</h2>";
echo "<pre>";

try {
    $controller = new HistoryController();
    
    echo "1. Llamando a processHistoryLogic()...\n";
    $controller->processHistoryLogic();
    
    echo "2. Obteniendo datos del historial...\n";
    
    // Obtener datos directamente del modelo
    require_once __DIR__ . '/Model/HistoryModel.php';
    $historyModel = new HistoryModel();
    
    $data = $historyModel->getAll(10, 0, []);
    
    echo "   Total registros en BD: " . ($data['total'] ?? 0) . "\n";
    echo "   Registros obtenidos: " . count($data['historial'] ?? []) . "\n";
    
    if (!empty($data['historial'])) {
        echo "\n3. Estructura de los primeros 2 registros:\n";
        foreach (array_slice($data['historial'], 0, 2) as $i => $item) {
            echo "   Registro " . ($i + 1) . ":\n";
            foreach ($item as $key => $value) {
                if (in_array($key, ['id', 'evento', 'tipo_evento', 'detalle', 'created_at', 'usuario_nombre'])) {
                    echo "     $key: " . (is_array($value) ? print_r($value, true) : $value) . "\n";
                }
            }
            echo "\n";
        }
    }
    
    echo "4. Verificando método getAll() del HistoryModel...\n";
    echo "   ¿Devuelve array 'historial'? " . (isset($data['historial']) ? '✓ SI' : '✗ NO') . "\n";
    echo "   ¿El array tiene datos? " . (!empty($data['historial']) ? '✓ SI' : '✗ NO') . "\n";
    
    // Verificar si hay problemas con los JOINS
    echo "\n5. Probando consulta SQL directa...\n";
    $db = DB::get();
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
            ORDER BY h.created_at DESC
            LIMIT 3";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $directResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Resultados directos de SQL: " . count($directResults) . " registros\n";
    if (!empty($directResults)) {
        echo "   Primer registro:\n";
        print_r($directResults[0]);
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "</pre>";
