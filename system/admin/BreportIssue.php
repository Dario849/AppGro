<?php
header('Content-Type: application/json; charset=utf-8');
require dirname(__DIR__, 2) . '/system/resources/database.php';
require dirname(__DIR__, 2) . '/system/resources/phpMailer.php';
class reportIssue
{
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
            $sql = "INSERT INTO reportes_problemas (id_emisor, id_usuario, descripcion, fecha_reporte) 
                    VALUES (:id_emisor, :id_usuario, :descripcion, NOW())";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_emisor' => $data['visitor_id'],
                ':id_usuario' => $data['uid'],
                ':descripcion' => $data['report']
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Reporte enviado correctamente']);
            $this->enviarReportesSoporte();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al enviar reporte: ' . $e->getMessage()]);
        }
    }
    // Función para enviar correo de notificación al soporte técnico con últimos 5 reportes recientes + 5 anteriores a esos recientes.
    // Función primero comprueba tabla reportes_problemas, si hay más de 5 reportes con campo "enviado" = 0, envía correo con últimos 5 + 5 anteriores, actualiza tabla para marcar esos reportes como notificados.
    private function enviarReportesSoporte()
    {
        try {
            // Contar reportes no enviados
            $countSql = "SELECT COUNT(*) FROM reportes_problemas WHERE enviado = 0";
            $countStmt = $this->pdo->query($countSql);
            $reportCount = $countStmt->fetchColumn();

            if ($reportCount >= 5) {
                // Obtener últimos 10 reportes
                $fetchSql = "SELECT * FROM reportes_problemas WHERE enviado = 0 ORDER BY fecha_reporte DESC LIMIT 10";
                $fetchStmt = $this->pdo->query($fetchSql);
                $reports = $fetchStmt->fetchAll(PDO::FETCH_ASSOC);

                // Construir cuerpo del correo
                $emailBody = "<p>Se han recibido nuevos reportes de problemas:</p>";
                $emailBody .= "<table border='1' style='width: 100%;'>";
                $emailBody .= "<tr><th>ID Emisor</th><th>ID Usuario</th><th>Descripción</th><th>Fecha Reporte</th></tr>";
                foreach ($reports as $report) {
                    $emailBody .= "<tr>";
                    $emailBody .= "<td>{$report['id_emisor']}</td>";
                    $emailBody .= "<td>{$report['id_usuario']}</td>";
                    $emailBody .= "<td>{$report['descripcion']}</td>";
                    $emailBody .= "<td>{$report['fecha_reporte']}</td>";
                    $emailBody .= "</tr>";
                }

                // Enviar correo al soporte técnico
                enviarMailSoporte($_ENV['SUPPORT_EMAIL'], 'Nuevos Reportes de Problemas', $emailBody);

                // Marcar reportes como enviados
                $updateSql = "UPDATE reportes_problemas SET enviado = 1 WHERE id IN (" . implode(',', array_column($reports, 'id')) . ")";
                $this->pdo->exec($updateSql);
            }
        } catch (Exception $e) {
            error_log("Error al enviar reportes al soporte: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Error al notificar soporte: ' . $e->getMessage()]);

        }
    }
}
$collector = new reportIssue();
$collector->handle('reportIssue', $_POST);
