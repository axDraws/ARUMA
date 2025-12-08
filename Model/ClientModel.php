<?php
// Model/ClientModel.php
require_once __DIR__ . '/../app/config.php';

class ClientModel {
    private PDO $db;
    
    public function __construct() {
        $this->db = DB::get();
    }
    
    /* ============================================================
       OBTENER TODOS LOS CLIENTES
    ============================================================ */
    public function getAllClients() {
        $sql = "SELECT * FROM users WHERE role = 'client' ORDER BY nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /* ============================================================
       BUSCAR CLIENTE POR ID - MÉTODO find()
    ============================================================ */
    public function find($id) {
        $sql = "SELECT * FROM users WHERE id = :id AND role = 'client' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /* ============================================================
       BUSCAR CLIENTE POR EMAIL
    ============================================================ */
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email AND role = 'client' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /* ============================================================
       CREAR NUEVO CLIENTE
    ============================================================ */
    public function create($nombre, $email, $password_hash) {
        $sql = "INSERT INTO users (role, nombre, email, password_hash) 
                VALUES ('client', :nombre, :email, :password_hash)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':password_hash' => $password_hash
        ]);
    }
    
    /* ============================================================
       ACTUALIZAR CLIENTE
    ============================================================ */
    public function update($id, $data) {
        $sql = "UPDATE users SET 
                nombre = :nombre,
                email = :email,
                telefono = :telefono,
                fecha_nac = :fecha_nac,
                direccion = :direccion
                WHERE id = :id AND role = 'client'";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'] ?? '',
            ':email' => $data['email'] ?? '',
            ':telefono' => $data['telefono'] ?? null,
            ':fecha_nac' => $data['fecha_nac'] ?? null,
            ':direccion' => $data['direccion'] ?? null,
            ':id' => $id
        ]);
    }
    
    /* ============================================================
       ELIMINAR CLIENTE
    ============================================================ */
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = :id AND role = 'client'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    /* ============================================================
       CONTAR TOTAL DE CLIENTES
    ============================================================ */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'client'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
