<?php
header('Content-Type: application/json');
require __DIR__ . '/../resources/database.php';   // conexión PDO
$pdo = DB::connect();
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
     ORDER BY fecha DESC LIMIT 100");
    $oldBalances = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($oldBalances);
    exit();
}catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
}