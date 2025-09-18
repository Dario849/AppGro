<?php
header('Content-Type: application/json');
require dirname(__DIR__, levels: 2) . '\system\resources\database.php';

// Recibir JSON desde fetch
$data = json_decode(file_get_contents("php://input"), true);
$criterio = $data['criterio'] ?? '';

if (!$criterio) {
    echo json_encode(["success" => false, "error" => "Falta criterio"]);
    exit;
}

switch ($criterio) {
    case 'semanal': $dias = 7; break;
    case 'mensual': $dias = 30; break;
    case 'anual':   $dias = 365; break;
    default:
        echo json_encode(["success" => false, "error" => "Criterio inválido"]);
        exit;
}

// Rango de fechas (de hoy hacia atrás)
$hoy = date('Y-m-d H:i:s');                  // ahora con hora
$limite = date('Y-m-d H:i:s', strtotime("-$dias days"));

// Conexión mysqli
try {
    // Baja lógica usando fecha_hora_inicio
  $stmt = $pdo->prepare("UPDATE tareas 
                      SET baja_logica = 1 
                      WHERE (estado = 'completada' OR estado = 'cancelada') 
                      AND fecha_hora_inicio BETWEEN ? AND ?");
  $stmt->execute([$limite,$hoy]);
  $affectedRows = $stmt->rowCount();
  echo json_encode(["success" => true, "eliminadas" => $affectedRows]);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

