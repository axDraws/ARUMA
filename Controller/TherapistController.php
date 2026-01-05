<?php
require_once __DIR__ . '/../Model/TherapistModel.php';
require_once __DIR__ . '/../Model/HistoryModel.php';

class TherapistController
{
    private $model;
    private $historyModel;

    public function __construct()
    {
        $this->model = new TherapistModel();
        $this->historyModel = new HistoryModel();
    }

    // API: Obtener todos los terapeutas
    public function getAllTherapistsApi()
    {
        header('Content-Type: application/json');
        try {
            $therapists = $this->model->getAllTherapists();
            echo json_encode($therapists);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener terapeutas: ' . $e->getMessage()]);
        }
    }

    // API: Obtener terapeuta por ID
    public function getTherapistByIdApi()
    {
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
            return;
        }

        try {
            $therapist = $this->model->getTherapistById($id);
            if ($therapist) {
                echo json_encode($therapist);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Terapeuta no encontrado']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener terapeuta']);
        }
    }

    // API: Crear terapeuta
    public function createTherapistApi()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'especialidad' => $_POST['especialidad'] ?? '',
            'telefono' => $_POST['telefono'] ?? ''
        ];

        if (empty($data['nombre'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nombre es requerido']);
            return;
        }

        try {
            if ($this->model->create($data)) {
                // Registrar historial
                if (isset($_SESSION['user_id'])) {
                    $this->historyModel->log(
                        'Terapeuta Creado',
                        null,
                        $_SESSION['user_id'],
                        "Se creó el terapeuta: " . $data['nombre'],
                        'terapeuta'
                    );
                }
                echo json_encode(['success' => true, 'message' => 'Terapeuta creado exitosamente']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al crear terapeuta']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error de servidor: ' . $e->getMessage()]);
        }
    }

    // API: Actualizar terapeuta
    public function updateTherapistApi()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
            return;
        }

        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'especialidad' => $_POST['especialidad'] ?? '',
            'telefono' => $_POST['telefono'] ?? ''
        ];

        try {
            if ($this->model->update($id, $data)) {
                // Registrar historial
                if (isset($_SESSION['user_id'])) {
                    $this->historyModel->log(
                        'Terapeuta Actualizado',
                        null,
                        $_SESSION['user_id'],
                        "Se actualizó el terapeuta ID: $id",
                        'terapeuta'
                    );
                }
                echo json_encode(['success' => true, 'message' => 'Terapeuta actualizado exitosamente']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al actualizar terapeuta']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error de servidor']);
        }
    }

    // API: Eliminar terapeuta
    public function deleteTherapistApi()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
            return;
        }

        try {
            if ($this->model->delete($id)) {
                // Registrar historial
                if (isset($_SESSION['user_id'])) {
                    $this->historyModel->log(
                        'Terapeuta Eliminado',
                        null,
                        $_SESSION['user_id'],
                        "Se eliminó (soft delete) el terapeuta ID: $id",
                        'terapeuta'
                    );
                }
                echo json_encode(['success' => true, 'message' => 'Terapeuta eliminado exitosamente']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al eliminar terapeuta']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error de servidor']);
        }
    }
}
