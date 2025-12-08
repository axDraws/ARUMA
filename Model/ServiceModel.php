<?php
require_once __DIR__ . '/../app/config.php';

class ServiceModel {
    private PDO $db;
    
    public function __construct() {
        $this->db = DB::get();
    }
    
    public function getAllServices() {
        $stmt = $this->db->query("SELECT * FROM services WHERE activo = 1 ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // MÉTODO REQUERIDO POR AdminController.php
    public function find($id) {
        return $this->getServiceById($id);
    }
    
    // Método original que ya tenías
    public function getServiceById($id) {
        $stmt = $this->db->prepare("SELECT * FROM services WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
