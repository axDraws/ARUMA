<?php
require_once __DIR__ . '/../app/config.php';

class TherapistModel {
    private PDO $db;
    
    public function __construct() {
        $this->db = DB::get();
    }
    
    public function getAllTherapists() {
        $stmt = $this->db->query("SELECT * FROM therapists WHERE activo = 1 ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTherapistById($id) {
        $stmt = $this->db->prepare("SELECT * FROM therapists WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
