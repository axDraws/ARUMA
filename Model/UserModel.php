<?php
require_once __DIR__ . '/../app/config.php';

class UserModel {

    private PDO $db;

    public function __construct() {
        $this->db = DB::get();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function create($nombre, $email, $password_hash) {
        $stmt = $this->db->prepare("INSERT INTO users (role, nombre, email, password_hash)
                                    VALUES ('client', :nombre, :email, :pass, NOW())");
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':pass' => $password_hash
        ]);

        return $this->db->lastInsertId();
    }
}

