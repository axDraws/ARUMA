<?php
require_once __DIR__ . '/../Model/UserModel.php';

class AuthController {

    public function login() {

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            echo "<script>alert('Correo o contraseña incorrectos'); window.location='/';</script>";
            exit;
        }

        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: /administrador");
        } else {
            header("Location: /cliente");
        }
        exit;
    }

    public function register() {

        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $userModel = new UserModel();
        $userModel->create($nombre, $email, $password);

        echo "<script>alert('Usuario registrado correctamente'); window.location='/';</script>";
    }
}

