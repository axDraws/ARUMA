<?php
require_once __DIR__ . '/app/config.php';

try {
    $db = DB::get();
    echo "✓ Conexión exitosa a MySQL<br>";
    
    // Prueba si existen las tablas
    $stmt = $db->query("SELECT * FROM users LIMIT 1");
    echo "✓ Tabla 'users' existe<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>