<?php
header('Content-Type: application/json; charset=utf-8');
require dirname(__DIR__, 2) . '\system\resources\database.php';
class reportIssue{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DB::connect();
    }
    public function handle($action, $data)
    {
        switch ($action) {
            case 'reportIssue':
                return $this->reportIssue($data);
            default:
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        }
    }
    private function reportIssue($data)
    {
        try {
            $sql = "INSERT INTO reportes_problemas (id_usuario, descripcion, fecha_reporte) 
                    VALUES (:id_usuario, :descripcion, NOW())";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_usuario' => $data['uid'],
                ':descripcion' => $data['report']
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Reporte enviado correctamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al enviar reporte: ' . $e->getMessage()]);
        }
    }
}
$collector = new reportIssue();
$collector->handle('reportIssue', $_POST);
