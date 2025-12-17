<?php
// api/transacciones.php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../resources/database.php';   // conexión PDO
$pdo = DB::connect();

session_start(); // o token que uses

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT 
    t.id,
    t.fecha,
    t.monto,
    t.tipo,
    t.dato,
    t.dato_cantidad,
    t.detalle,
    CONCAT(u.nombre, ' ', u.apellido) AS usuario_responsable
    FROM transacciones t
    INNER JOIN usuarios u ON t.id_usuario = u.id;
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $rows]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
}
