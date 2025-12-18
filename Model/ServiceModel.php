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

    public function create($nombre, $duracion, $precio, $categoria, $descripcion) {
        $stmt = $this->db->prepare("INSERT INTO services (nombre, duracion_min, precio, categoria, descripcion, activo) VALUES (:nombre, :duracion, :precio, :categoria, :descripcion, 1)");
        $stmt->execute([
            ':nombre' => $nombre,
            ':duracion' => $duracion,
            ':precio' => $precio,
            ':categoria' => $categoria,
            ':descripcion' => $descripcion
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['nombre'])) {
            $fields[] = "nombre = :nombre";
            $params[':nombre'] = $data['nombre'];
        }
        if (isset($data['duracion_min'])) {
            $fields[] = "duracion_min = :duracion";
            $params[':duracion'] = $data['duracion_min'];
        }
        if (isset($data['precio'])) {
            $fields[] = "precio = :precio";
            $params[':precio'] = $data['precio'];
        }
        if (isset($data['categoria'])) {
            $fields[] = "categoria = :categoria";
            $params[':categoria'] = $data['categoria'];
        }
        if (isset($data['descripcion'])) {
            $fields[] = "descripcion = :descripcion";
            $params[':descripcion'] = $data['descripcion'];
        }
        if (isset($data['activo'])) {
            $fields[] = "activo = :activo";
            $params[':activo'] = $data['activo'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE services SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        // Soft delete usually safer, but schema says 'activo' defaults to 1.
        // Let's implement soft delete by setting activo = 0 if that's the intention, 
        // OR hard delete if the user wants true deletion.
        // Given existing patterns (ClientModel generally hard deletes unless specified otherwise, but check schema constraints).
        // Reservations table has FOREIGN KEY (servicio_id) REFERENCES services(id) ON DELETE RESTRICT
        // So we probably CANNOT hard delete if there are reservations.
        // Let's try soft delete by updating active status if hard delete fails, or just try hard delete and let controller handle exception.
        // BUT, looking at the plan, I proposed "delete".
        // The table has an 'activo' column (TINYINT).
        // Let's implement hard delete first, as that's standard for "Delete" buttons unless "Deactivate" is requested.
        // If it fails due to foreign key, the controller will catch it.
        // Wait, for services, it's often better to check if it has usage.
        
        $stmt = $this->db->prepare("DELETE FROM services WHERE id = :id");
        try {
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            // Fallback or just rethrow?
            throw $e;
        }
    }
}
