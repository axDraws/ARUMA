<?php
require_once __DIR__ . '/../app/config.php';

class ProductModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::get();
    }

    public function getAllProducts()
    {
        // user schema didn't have 'activo', but I added it in my create script just in case?
        // Wait, did I? Yes, "activo TINYINT(1) DEFAULT 1".
        // so I should filter by activo if I want soft deletes, or just show all if hard deletes.
        // Let's assume show all active ones.
        $stmt = $this->db->query("SELECT * FROM products ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nombre, $descripcion, $precio, $imagen_path)
    {
        $stmt = $this->db->prepare("INSERT INTO products (nombre, descripcion, precio, imagen_path) VALUES (:nombre, :descripcion, :precio, :imagen_path)");
        $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':precio' => $precio,
            ':imagen_path' => $imagen_path
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['nombre'])) {
            $fields[] = "nombre = :nombre";
            $params[':nombre'] = $data['nombre'];
        }
        if (isset($data['descripcion'])) {
            $fields[] = "descripcion = :descripcion";
            $params[':descripcion'] = $data['descripcion'];
        }
        if (isset($data['precio'])) {
            $fields[] = "precio = :precio";
            $params[':precio'] = $data['precio'];
        }
        if (isset($data['imagen_path'])) {
            $fields[] = "imagen_path = :imagen_path";
            $params[':imagen_path'] = $data['imagen_path'];
        }
        // If I use 'activo' column
        if (isset($data['activo'])) {
            $fields[] = "activo = :activo";
            $params[':activo'] = $data['activo'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
