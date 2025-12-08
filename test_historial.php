<?php
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/Model/HistoryModel.php';

session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

echo "<h1>Prueba del Sistema de Historial</h1>";

try {
    $historyModel = new HistoryModel();
    
    echo "<h2>1. Estructura de la tabla:</h2>";
    $structure = $historyModel->getTableStructure();
    echo "<pre>" . print_r($structure, true) . "</pre>";
    
    echo "<h2>2. ¿Hay datos?</h2>";
    $hasData = $historyModel->hasData();
    echo $hasData ? "SÍ hay datos" : "NO hay datos";
    
    echo "<h2>3. Intentar agregar un registro de prueba:</h2>";
    $result = $historyModel->log(
        'Prueba de Historial',
        null,
        1,
        'Este es un registro de prueba',
        'reserva'
    );
    
    echo $result ? "✓ Registro agregado correctamente" : "✗ Error al agregar";
    
    echo "<h2>4. Obtener todos los registros:</h2>";
    $data = $historyModel->getAll(10, 0, []);
    echo "<pre>" . print_r($data, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
