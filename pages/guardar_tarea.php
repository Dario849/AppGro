<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "app_campo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Error de conexión"]);
    exit;
}

// Obtener y validar los datos JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["fecha_hora_inicio"], $data["fecha_hora_fin"], $data["texto"])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos o malformateados"]);
    exit;
}

$estado = "activa";
$fecha_inicio = $data["fecha_hora_inicio"] . " 00:00:00";
$fecha_fin = $data["fecha_hora_fin"] . " 00:00:00";
$texto = $conn->real_escape_string($data["texto"]);

$sql = "INSERT INTO tareas (estado, fecha_hora_inicio, fecha_hora_fin, texto) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $estado, $fecha_inicio, $fecha_fin, $texto);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
}

$stmt->close();
$conn->close();
?>
