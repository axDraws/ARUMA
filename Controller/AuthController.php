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

        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $passwordRaw = $_POST['password'] ?? '';

        if ($nombre === '' || $email === '' || $passwordRaw === '') {
            echo "<script>alert('Todos los campos son requeridos'); window.history.back();</script>";
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Correo inválido'); window.history.back();</script>";
            exit();
        }

        $userModel = new UserModel();
        // verificar si ya existe
        if ($userModel->findByEmail($email)) {
            echo "<script>alert('Ya existe una cuenta con ese correo'); window.history.back();</script>";
            exit();
        }

        $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

        try {
            $id = $userModel->create($nombre, $email, $password);
            if ($id) {
                echo "<script>alert('Usuario registrado correctamente'); window.location='/';</script>";
                exit();
            }
        } catch (PDOException $e) {
            // código 23000 = integrity constraint violation (p.ej. duplicate entry)
            if ($e->getCode() == '23000') {
                echo "<script>alert('Correo ya registrado'); window.history.back();</script>";
                exit();
            }
            echo "<script>alert('Error en el registro: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
            exit();
        }
    }
}

