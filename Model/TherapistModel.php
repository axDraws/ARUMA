<?php
require_once __DIR__ . '/../app/config.php';

class TherapistModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::get();
    }

    public function getAllTherapists()
    {
        $stmt = $this->db->query("SELECT * FROM therapists WHERE activo = 1 ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTherapistById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM therapists WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO therapists (nombre, especialidad, telefono, activo) VALUES (:nombre, :especialidad, :telefono, 1)");
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':especialidad' => $data['especialidad'] ?? '',
            ':telefono' => $data['telefono'] ?? ''
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE therapists SET nombre = :nombre, especialidad = :especialidad, telefono = :telefono WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':nombre' => $data['nombre'],
            ':especialidad' => $data['especialidad'] ?? '',
            ':telefono' => $data['telefono'] ?? ''
        ]);
    }

    public function delete($id)
    {
        // Soft delete
        $stmt = $this->db->prepare("UPDATE therapists SET activo = 0 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
