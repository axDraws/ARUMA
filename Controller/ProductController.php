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
            $descripcion = $_POST['descripcion'] ?? '';
            $imagen_path = $_POST['imagen_path'] ?? ''; // Handle generic path/url for now

            $id = $this->productModel->create($nombre, $descripcion, $precio, $imagen_path);

            if ($id) {
                $this->logAdminAction('Producto Creado', "Producto: $nombre", $id);
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

            $data = [
                'nombre' => $_POST['nombre'] ?? $current['nombre'],
                'precio' => $_POST['precio'] ?? $current['precio'],
                'descripcion' => $_POST['descripcion'] ?? $current['descripcion'],
                'imagen_path' => $_POST['imagen_path'] ?? $current['imagen_path']
            ];

            // Log changes
            $changes = [];
            if ($current['nombre'] != $data['nombre'])
                $changes[] = "Nombre";
            if ($current['precio'] != $data['precio'])
                $changes[] = "Precio";

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
