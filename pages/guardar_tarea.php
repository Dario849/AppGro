<?php
header('Content-Type: application/json');

require dirname(__DIR__, 1) . '/system/resources/database.php';
$pdo = DB::connect();

// Obtener y validar los datos JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["fecha_hora_inicio"], $data["fecha_hora_fin"], $data["texto"])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos o malformateados"]);
    exit;
}

$estado = "activa";
$fecha_inicio = $data["fecha_hora_inicio"] . " 00:00:00";
$fecha_fin = $data["fecha_hora_fin"] . " 00:00:00";
$texto = $data["texto"];

$sql = "INSERT INTO tareas (estado, fecha_hora_inicio, fecha_hora_fin, texto) VALUES (?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute( [$estado, $fecha_inicio, $fecha_fin, $texto]);

if ($stmt->rowCount() > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "No se insertó el registro"]);
}

$stmt=null;
$conn=null;
?>
