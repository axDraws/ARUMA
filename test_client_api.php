<?php
// test_client_api.php

// 1. Setup Mock Environment
$_SERVER['REQUEST_URI'] = '/api/clientes/create';
$_SERVER['REQUEST_METHOD'] = 'POST';

// Mock Admin Session
// We need to make sure session_start() in index.php finds these values
// Since we are CLI, we can pre-populate $_SESSION but we need to ensure session persistence if index.php calls session_start()
// Actually, in CLI, session_start() creates a session. We can just populate $_SESSION after calling it once, 
// or before if auto_start is off.
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

// Mock POST Data
$_POST['nombre'] = 'Curl Test User ' . time();
$_POST['email'] = 'curl_test_' . time() . '@example.com';
$_POST['password'] = 'secret123';

echo "Testing API Route: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Mocking Admin User ID: " . $_SESSION['user_id'] . "\n";

// 2. Execute Router
// We use a buffer to capture output because index.php might output headers/JSON
ob_start();
try {
    require 'index.php';
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
}
$output = ob_get_clean();

// 3. Display Results
echo "---------------------------------------------------\n";
echo "Response Output:\n";
echo $output . "\n";
echo "---------------------------------------------------\n";
