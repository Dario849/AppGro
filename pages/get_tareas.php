<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "app_campo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión"]);
    exit;
}

$estado = $_GET['estado'] ?? 'todas';
$orden = $_GET['orden'] ?? '';
$direccion = strtolower($_GET['direccion'] ?? 'asc');

// NUEVO: Parámetros para filtrar por mes y año
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : 0;
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : 0;

// Validación dirección segura
if (!in_array($direccion, ['asc', 'desc'])) {
    $direccion = 'asc';
}

// Validación estado
$estados_permitidos = ['activa', 'completada', 'cancelada', 'todas'];
if (!in_array($estado, $estados_permitidos)) {
    http_response_code(400);
    echo json_encode(["error" => "Estado inválido"]);
    exit;
}

$sql = "SELECT * FROM tareas WHERE baja_logica = 0";
$params = [];
$types = "";

// Filtro por estado (si no es todas)
if ($estado !== 'todas') {
    $sql .= " AND estado = ?";
    $params[] = $estado;
    $types .= "s";
}

// NUEVO: filtro por mes y año si están seteados y válidos
if ($mes > 0 && $anio > 0) {
    $fechaInicio = "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-01";
    $fechaFin = date("Y-m-t", strtotime($fechaInicio));

    $sql .= " AND fecha_hora_inicio BETWEEN ? AND ?";
    $params[] = $fechaInicio;
    $params[] = $fechaFin;
    $types .= "ss";
}

// Orden por vencimiento si corresponde
if ($orden === 'vencimiento') {
    $sql .= " ORDER BY fecha_hora_fin $direccion";
}

$stmt = $conn->prepare($sql);

// Si hay parámetros, los vinculamos
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$tareas = [];
while ($row = $result->fetch_assoc()) {
    $tareas[] = $row;
}

echo json_encode($tareas);

$stmt->close();
$conn->close();
?>
