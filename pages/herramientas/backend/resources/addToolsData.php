<?php
session_start();
class addToolsData
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function handle($action, $data)
    {
        switch ($action) {
            case 'addTool':
            case 'crearHerramienta':
                return $this->agregarHerramienta($data);
            default:
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        }
    }

    private function agregarHerramienta($data)
    {
        try {
            $sql = "INSERT INTO herramientas 
                    (nombre, marca, modelo, id_tipo_herramienta, estado, descripcion, fecha_compra, id_ultimo_usuario_responsable, horas_uso_total) 
                    VALUES 
                    (:nombre, :marca, :modelo, :id_tipo_herramienta, :estado, :descripcion, :fecha_compra, :id_ultimo_usuario_responsable, :horas_uso_total)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $data['herramienta']['nombre'],
                ':marca' => $data['herramienta']['marca'],
                ':modelo' => $data['herramienta']['modelo'],
                ':id_tipo_herramienta' => $data['herramienta']['id_tipo_herramienta'],
                ':estado' => $data['herramienta']['estado'],
                ':descripcion' => $data['herramienta']['descripcion'],
                ':fecha_compra' => $data['herramienta']['fecha_compra'],
                ':horas_uso_total' => $data['herramienta']['horas_uso_total'] ?? 0,
                ':id_ultimo_usuario_responsable' => $_SESSION['user_id']
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Herramienta agregada correctamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al agregar herramienta: ' . $e->getMessage()]);
        }
    }
}