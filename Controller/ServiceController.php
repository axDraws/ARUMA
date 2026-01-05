<?php
require_once __DIR__ . '/../Model/ServiceModel.php';
require_once __DIR__ . '/../Model/HistoryModel.php';

class ServiceController
{

    private $serviceModel;
    private $historyModel;

    public function __construct()
    {
        $this->serviceModel = new ServiceModel();
        $this->historyModel = new HistoryModel();
    }

    /* ============================================================
        VALIDAR ADMIN
    ============================================================ */
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

    /* ============================================================
        LOG DE ACCIONES
    ============================================================ */
    private function logAdminAction($evento, $detalle = null, $id_objeto = null, $anterior_valor = null, $nuevo_valor = null)
    {
        $user_id = $_SESSION['user_id'] ?? null;

        $this->historyModel->log(
            $evento,           // evento
            null,              // reservation_id (null)
            $user_id,          // user_id (quien hace la accion)
            $detalle,          // detalle
            'servicio',        // tipo_evento
            $anterior_valor,   // anterior_valor
            $nuevo_valor       // nuevo_valor
        );
    }

    /* ============================================================
        API: OBTENER TODOS LOS SERVICIOS (ADMIN)
    ============================================================ */
    public function getAllServicesApi()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        try {
            $servicios = $this->serviceModel->getAllServices();
            echo json_encode($servicios);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener servicios']);
        }
    }

    /* ============================================================
        API: OBTENER TODOS LOS SERVICIOS (PUBLICO/CLIENTE)
    ============================================================ */
    public function getPublicServicesApi()
    {
        // Puedes agregar validar sesión si es privado
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si quieres que sea totalmente público, quita esto.
        // Si quieres solo usuarios registrados:
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        header('Content-Type: application/json');

        try {
            // Podríamos filtrar solo activos
            $servicios = $this->serviceModel->getAllServices();
            $activos = array_filter($servicios, function ($s) {
                return isset($s['activo']) ? $s['activo'] == 1 : true;
            });
            echo json_encode(array_values($activos));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener servicios']);
        }
    }

    /* ============================================================
        API: OBTENER UN SERVICIO POR ID
    ============================================================ */
    public function getServiceByIdApi()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        if (empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID no proporcionado']);
            return;
        }

        try {
            $servicio = $this->serviceModel->find($_GET['id']);
            if ($servicio) {
                echo json_encode($servicio);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Servicio no encontrado']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno']);
        }
    }

    /* ============================================================
        API: CREAR SERVICIO
    ============================================================ */
    public function createServiceApi()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        if (empty($_POST['nombre']) || empty($_POST['duracion_min']) || empty($_POST['precio'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nombre, Duración y Precio son requeridos']);
            return;
        }

        try {
            $nombre = $_POST['nombre'];
            $duracion = $_POST['duracion_min'];
            $precio = $_POST['precio'];
            $categoria = $_POST['categoria'] ?? 'General';
            $descripcion = $_POST['descripcion'] ?? '';

            $id = $this->serviceModel->create($nombre, $duracion, $precio, $categoria, $descripcion);

            if ($id) {
                $detalle = "Servicio creado: " . $nombre . " ($duracion min, $$precio)";
                $this->logAdminAction('Servicio Creado (Admin)', $detalle, $id);

                echo json_encode(['status' => 'ok', 'id' => $id]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo crear el servicio']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    /* ============================================================
        API: ACTUALIZAR SERVICIO
    ============================================================ */
    public function updateServiceApi()
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
            $servicioActual = $this->serviceModel->find($_POST['id']);
            if (!$servicioActual) {
                http_response_code(404);
                echo json_encode(['error' => 'Servicio no encontrado']);
                return;
            }

            $data = [
                'nombre' => $_POST['nombre'] ?? $servicioActual['nombre'],
                'duracion_min' => $_POST['duracion_min'] ?? $servicioActual['duracion_min'],
                'precio' => $_POST['precio'] ?? $servicioActual['precio'],
                'categoria' => $_POST['categoria'] ?? $servicioActual['categoria'],
                'descripcion' => $_POST['descripcion'] ?? $servicioActual['descripcion'],
                'activo' => $_POST['activo'] ?? $servicioActual['activo']
            ];

            // Datos anteriores para log
            $anterior = [
                'nombre' => $servicioActual['nombre'],
                'duracion_min' => $servicioActual['duracion_min'],
                'precio' => $servicioActual['precio'],
                'categoria' => $servicioActual['categoria'],
                'descripcion' => $servicioActual['descripcion'],
                'activo' => $servicioActual['activo']
            ];

            $ok = $this->serviceModel->update($_POST['id'], $data);

            if ($ok) {
                // Registrar cambios
                $cambios = [];
                if ($anterior['nombre'] != $data['nombre'])
                    $cambios[] = "Nombre cambiado";
                if ($anterior['precio'] != $data['precio'])
                    $cambios[] = "Precio cambiado";
                if ($anterior['duracion_min'] != $data['duracion_min'])
                    $cambios[] = "Duración cambiada";

                $detalle = !empty($cambios) ? implode(", ", $cambios) : "Servicio actualizado";
                $this->logAdminAction('Servicio Actualizado (Admin)', $detalle, $_POST['id'], $anterior, $data);

                echo json_encode(['status' => 'ok']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo actualizar']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    /* ============================================================
        API: ELIMINAR SERVICIO
    ============================================================ */
    public function deleteServiceApi()
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
            $servicio = $this->serviceModel->find($_POST['id']);
            if (!$servicio) {
                http_response_code(404);
                echo json_encode(['error' => 'Servicio no existe']);
                return;
            }

            // Intentar eliminar
            try {
                $ok = $this->serviceModel->delete($_POST['id']);

                if ($ok) {
                    $detalle = "Servicio eliminado: " . $servicio['nombre'];
                    $this->logAdminAction('Servicio Eliminado (Admin)', $detalle, $_POST['id'], $servicio, null);
                    echo json_encode(['status' => 'ok']);
                } else {
                    echo json_encode(['error' => 'No se pudo eliminar el servicio']);
                }
            } catch (PDOException $pdoEx) {
                // Si falla por FK constraint
                if ($pdoEx->getCode() == '23000') {
                    http_response_code(400);
                    echo json_encode(['error' => 'No se puede eliminar porque existen reservas asociadas. Desactívalo en su lugar.']);
                } else {
                    throw $pdoEx;
                }
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
        }
    }
}
