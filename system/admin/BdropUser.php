<?php
header('Content-Type: application/json');
require __DIR__ . '/../resources/database.php';   // conexión PDO
$pdo = DB::connect();
$usuario_id = (int) $_POST["dropUId"] ?? null;
$now = date("Y-m-d H:i:s");
try {
    $stmt = $pdo->prepare("UPDATE usuarios SET estado = 'inactivo', fecha_estado = ? WHERE id = ?");
    $stmt->execute([$now ,$usuario_id]);
    echo json_encode("Success");
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}