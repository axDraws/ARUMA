<?php
require_once __DIR__ . '/../app/config.php';

class ClientModel {
    private PDO $db;
    
    public function __construct() {
        $this->db = DB::get();
    }
    
    public function getAllClients() {
        $stmt = $this->db->query("SELECT id, nombre, email, telefono FROM users WHERE role = 'client' ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
