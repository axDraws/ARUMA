<?php
require_once __DIR__ . '/../Model/ProductModel.php';
require_once __DIR__ . '/../Model/HistoryModel.php';

class ProductController
{
    private $productModel;
    private $historyModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->historyModel = new HistoryModel();
    }

    private function checkAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
    }

    private function logAdminAction($evento, $detalle = null, $id_objeto = null, $anterior = null, $nuevo = null)
    {
        $user_id = $_SESSION['user_id'] ?? null;
        $this->historyModel->log(
            $evento,
            null,
            $user_id,
            $detalle,
            'producto', // Event type
            $anterior,
            $nuevo
        );
    }

    public function getAllProductsApi()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');
        try {
            $products = $this->productModel->getAllProducts();
            echo json_encode($products);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener productos']);
        }
    }

    public function getProductByIdApi()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        if (empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
            return;
        }

        try {
            $product = $this->productModel->find($_GET['id']);
            if ($product) {
                echo json_encode($product);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Producto no encontrado']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno']);
        }
    }

    private function handleImageUpload($categoria)
    {
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['imagen']['tmp_name']);

        if (!in_array($mime, $allowed)) {
            throw new Exception("Tipo de archivo no permitido: " . $mime);
        }

        // Determinar carpeta destino
        $categoriaFolder = strtolower($categoria) === 'salerm' ? 'salerm' : 'cuccio';
        $uploadDir = __DIR__ . '/../public/img/' . $categoriaFolder . '/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('prod_') . '.' . $ext;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
            return '../public/img/' . $categoriaFolder . '/' . $filename;
        }

        throw new Exception("Error al mover el archivo subido.");
    }

    public function createProductApi()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        if (empty($_POST['nombre']) || empty($_POST['precio'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nombre y Precio son requeridos']);
            return;
        }

        try {
            $nombre = $_POST['nombre'];
            $precio = $_POST['precio'];
            $categoria = $_POST['categoria'] ?? 'Cuccio'; // Default
            $descripcion = $_POST['descripcion'] ?? '';

            // Handle Upload
            $imagen_path = '';
            try {
                $uploadedPath = $this->handleImageUpload($categoria);
                if ($uploadedPath) {
                    $imagen_path = $uploadedPath;
                } else {
                    // Fallback to text input if any (backward compatibility) or generic
                    $imagen_path = $_POST['imagen_path'] ?? '';
                }
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
                return;
            }

            $id = $this->productModel->create($nombre, $categoria, $descripcion, $precio, $imagen_path);

            if ($id) {
                $this->logAdminAction('Producto Creado', "Producto: $nombre ($categoria)", $id);
                echo json_encode(['status' => 'ok', 'id' => $id]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo crear el producto']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    public function updateProductApi()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        if (empty($_POST['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
            return;
        }

        try {
            $current = $this->productModel->find($_POST['id']);
            if (!$current) {
                http_response_code(404);
                echo json_encode(['error' => 'Producto no encontrado']);
                return;
            }

            $nombre = $_POST['nombre'] ?? $current['nombre'];
            $categoria = $_POST['categoria'] ?? ($current['categoria'] ?? 'Cuccio');
            $precio = $_POST['precio'] ?? $current['precio'];
            $descripcion = $_POST['descripcion'] ?? $current['descripcion'];

            // Handle Upload
            $imagen_path = $current['imagen_path'];
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                try {
                    $uploaded = $this->handleImageUpload($categoria);
                    if ($uploaded)
                        $imagen_path = $uploaded;
                } catch (Exception $e) {
                    http_response_code(400);
                    echo json_encode(['error' => $e->getMessage()]);
                    return;
                }
            }

            $data = [
                'nombre' => $nombre,
                'precio' => $precio,
                'descripcion' => $descripcion,
                'imagen_path' => $imagen_path,
                'categoria' => $categoria
            ];

            // Log changes
            $changes = [];
            if ($current['nombre'] != $data['nombre'])
                $changes[] = "Nombre";
            if ($current['precio'] != $data['precio'])
                $changes[] = "Precio";
            if (($current['categoria'] ?? '') != $data['categoria'])
                $changes[] = "Categoría";
            if ($current['imagen_path'] != $data['imagen_path'])
                $changes[] = "Imagen";

            $ok = $this->productModel->update($_POST['id'], $data);

            if ($ok) {
                $detail = !empty($changes) ? "Actualizado: " . implode(', ', $changes) : "Producto actualizado";
                $this->logAdminAction('Producto Actualizado', $detail, $_POST['id'], $current, $data);
                echo json_encode(['status' => 'ok']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo actualizar']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno']);
        }
    }

    public function deleteProductApi()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        if (empty($_POST['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
            return;
        }

        try {
            $product = $this->productModel->find($_POST['id']);
            if (!$product) {
                http_response_code(404);
                echo json_encode(['error' => 'Producto no encontrado']);
                return;
            }

            $ok = $this->productModel->delete($_POST['id']);

            if ($ok) {
                $this->logAdminAction('Producto Eliminado', "Producto: " . $product['nombre'], $_POST['id'], $product, null);
                echo json_encode(['status' => 'ok']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo eliminar']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno ' . $e->getMessage()]);
        }
    }
}
