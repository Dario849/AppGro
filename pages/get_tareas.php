<?php
header('Content-Type: application/json');
require dirname(__DIR__, 1) . '/system/resources/database.php';
$pdo = DB::connect();

$estado = $_GET['estado'] ?? 'todas';
$orden = $_GET['orden'] ?? '';
$direccion = strtolower($_GET['direccion'] ?? 'asc');

// NUEVO: Parámetros para filtrar por mes y año
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : 0;
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : 0;

$sql = "SELECT * FROM tareas WHERE baja_logica = 0";
$params = [];

// Filtro por estado (si no es todas)
if ($estado !== 'todas') {
    $sql .= " AND estado = ?";
    $params[] = $estado;
}

// NUEVO: filtro por mes y año si están seteados y válidos
if ($mes > 0 && $anio > 0) {
    $fechaInicio = "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-01";
    $fechaFin = date("Y-m-t", strtotime($fechaInicio));

    $sql .= " AND fecha_hora_inicio BETWEEN ? AND ?";
    $params[] = $fechaInicio;
    $params[] = $fechaFin;
}

// Orden por vencimiento si corresponde
if ($orden === 'vencimiento') {
    $sql .= " ORDER BY fecha_hora_fin $direccion";
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($tareas);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}