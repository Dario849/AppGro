<?php
header('Content-Type: application/json');
require dirname(__DIR__, 1) . '/system/resources/database.php';
$pdo = DB::connect();

// Recibir JSON desde fetch
$data = json_decode(file_get_contents("php://input"), true);
$criterio = $data['criterio'] ?? '';

if (!$criterio) {
    echo json_encode(["success" => false, "error" => "Falta criterio"]);
    exit;
}

try {
    $hoy = date('Y-m-d');
    $sql = "";
    $params = [];

    switch ($criterio) {
        case 'semanal':
            // Este mes (del 01 hasta hoy)
            $inicio = date('Y-m-01');
            $fin = $hoy;
            $sql = "UPDATE tareas 
                    SET baja_logica = 1 
                    WHERE (estado = 'completada' OR estado = 'cancelada') 
                    AND DATE(fecha_hora_inicio) BETWEEN ? AND ?";
            $params = [$inicio, $fin];
            break;

        case 'mensual':
            // Este año (enero 01 hasta hoy)
            $inicio = date('Y-01-01');
            $fin = $hoy;
            $sql = "UPDATE tareas 
                    SET baja_logica = 1 
                    WHERE (estado = 'completada' OR estado = 'cancelada') 
                    AND DATE(fecha_hora_inicio) BETWEEN ? AND ?";
            $params = [$inicio, $fin];
            break;

        case 'anual':
            // Años anteriores
            $limite = date('Y-01-01');
            $sql = "UPDATE tareas 
                    SET baja_logica = 1 
                    WHERE (estado = 'completada' OR estado = 'cancelada') 
                    AND DATE(fecha_hora_inicio) < ?";
            $params = [$limite];
            break;

        default:
            echo json_encode(["success" => false, "error" => "Criterio inválido"]);
            exit;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $affectedRows = $stmt->rowCount();

    echo json_encode(["success" => true, "eliminadas" => $affectedRows]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}