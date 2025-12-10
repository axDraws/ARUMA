<?php
require_once __DIR__ . '/../Model/ClientModel.php';
require_once __DIR__ . '/../Model/HistoryModel.php';

class ClientController
{

    private $clientModel;
    private $historyModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
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
        LOG DE ACCIONES (Igual que en AdminController)
    ============================================================ */
    private function logAdminAction($evento, $detalle = null, $user_target_id = null, $anterior_valor = null, $nuevo_valor = null)
    {
        $user_id = $_SESSION['user_id'] ?? null;

        $this->historyModel->log(
            $evento,           // evento
            null,              // reservation_id (null para clientes)
            $user_id,          // user_id (quien hace la acción)
            $detalle,          // detalle
            'usuario',         // tipo_evento
            $anterior_valor,   // anterior_valor
            $nuevo_valor       // nuevo_valor
        );
    }

    /* ============================================================
        API: OBTENER TODOS LOS CLIENTES
    ============================================================ */
    public function getAllClientsApi()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        try {
            $clientes = $this->clientModel->getAllClients();
            echo json_encode($clientes);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener clientes']);
        }
    }

    /* ============================================================
        API: OBTENER UN CLIENTE POR ID
    ============================================================ */
    public function getClientByIdApi()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        if (empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID no proporcionado']);
            return;
        }

        try {
            $cliente = $this->clientModel->find($_GET['id']);
            if ($cliente) {
                echo json_encode($cliente);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Cliente no encontrado']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno']);
        }
    }

    /* ============================================================
        API: CREAR CLIENTE
    ============================================================ */
    public function createClientApi()
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        if (empty($_POST['nombre']) || empty($_POST['email']) || empty($_POST['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nombre, Email y Password son requeridos']);
            return;
        }

        try {
            // Verificar si correo ya existe
            $existe = $this->clientModel->findByEmail($_POST['email']);
            if ($existe) {
                http_response_code(400);
                echo json_encode(['error' => 'El correo electrónico ya está registrado']);
                return;
            }

            $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $ok = $this->clientModel->create($_POST['nombre'], $_POST['email'], $password_hash);

            if ($ok) {
                // Como create no retorna ID en ClientModel (según vi en tu código anterior), buscamos por email
                $nuevo = $this->clientModel->findByEmail($_POST['email']);

                $detalle = "Cliente creado por admin | Nombre: " . $_POST['nombre'] . " | Email: " . $_POST['email'];
                $this->logAdminAction('Cliente Creado (Admin)', $detalle, $nuevo['id'] ?? null);

                echo json_encode(['status' => 'ok', 'id' => $nuevo['id'] ?? null]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo crear el cliente']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    /* ============================================================
        API: ACTUALIZAR CLIENTE
    ============================================================ */
    public function updateClientApi()
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
            $clienteActual = $this->clientModel->find($_POST['id']);
            if (!$clienteActual) {
                http_response_code(404);
                echo json_encode(['error' => 'Cliente no encontrado']);
                return;
            }

            $data = [
                'nombre' => $_POST['nombre'] ?? $clienteActual['nombre'],
                'email' => $_POST['email'] ?? $clienteActual['email'],
                'telefono' => $_POST['telefono'] ?? $clienteActual['telefono'],
                'fecha_nac' => $_POST['fecha_nac'] ?? $clienteActual['fecha_nac'],
                'direccion' => $_POST['direccion'] ?? $clienteActual['direccion']
            ];

            // Datos para el log
            $anterior = [
                'nombre' => $clienteActual['nombre'],
                'email' => $clienteActual['email'],
                'telefono' => $clienteActual['telefono'],
                'direccion' => $clienteActual['direccion']
            ];

            $nuevo = [
                'nombre' => $data['nombre'],
                'email' => $data['email'],
                'telefono' => $data['telefono'],
                'direccion' => $data['direccion']
            ];

            $ok = $this->clientModel->update($_POST['id'], $data);

            if ($ok) {
                $cambios = [];
                if ($anterior['nombre'] != $nuevo['nombre'])
                    $cambios[] = "Nombre cambiado";
                if ($anterior['email'] != $nuevo['email'])
                    $cambios[] = "Email cambiado";
                if ($anterior['telefono'] != $nuevo['telefono'])
                    $cambios[] = "Teléfono actualizado";

                $detalle = !empty($cambios) ? implode(", ", $cambios) : "Datos actualizados";

                $this->logAdminAction('Cliente Actualizado (Admin)', $detalle, $_POST['id'], $anterior, $nuevo);

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
        API: ELIMINAR CLIENTE
    ============================================================ */
    public function deleteClientApi()
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
            $cliente = $this->clientModel->find($_POST['id']);
            if (!$cliente) {
                http_response_code(404);
                echo json_encode(['error' => 'Cliente no existe']);
                return;
            }

            // Log antes de borrar
            $detalle = "Cliente eliminado: " . $cliente['nombre'];
            $this->logAdminAction('Cliente Eliminado (Admin)', $detalle, $_POST['id'], $cliente, null);

            $ok = $this->clientModel->delete($_POST['id']);

            echo json_encode(['status' => $ok ? 'ok' : 'error']);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar (posiblemente tiene reservas asociadas)']);
        }
    }
}
