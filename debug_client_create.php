<?php
// debug_client_create.php
require_once __DIR__ . '/Model/ClientModel.php';

echo "Testing Client Creation...\n";

$model = new ClientModel();

$nombre = "Test User " . time();
$email = "test" . time() . "@example.com";
$password = password_hash("123456", PASSWORD_DEFAULT);

echo "Attempting to create user: $nombre ($email)\n";

try {
    $result = $model->create($nombre, $email, $password);

    if ($result) {
        echo "SUCCESS: User created.\n";
        $user = $model->findByEmail($email);
        print_r($user);

        // Clean up
        $model->delete($user['id']);
        echo "Cleaned up (deleted) user.\n";
    } else {
        echo "FAILURE: create returned false.\n";
    }
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}
