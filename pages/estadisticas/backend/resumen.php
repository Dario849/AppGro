<?php
header('Content-Type: application/json');
require dirname(__DIR__,4) . '\system\resources\database.php';
$pdo = DB::connect();
try {
    $ventas = $pdo->query("SELECT SUM(monto) AS total FROM transacciones WHERE tipo = 1")->fetchColumn();
    $compras = $pdo->query("SELECT SUM(monto) AS total FROM transacciones WHERE tipo = 2")->fetchColumn();
    $ganado = $pdo->query("SELECT COUNT(*) FROM ganado")->fetchColumn();
    $cultivos = $pdo->query("SELECT COUNT(*) FROM cultivos")->fetchColumn();
    $tareas = $pdo->query("SELECT COUNT(*) FROM tareas WHERE estado = 'completada'")->fetchColumn();

    echo json_encode([
        'ventas' => $ventas ?: 0,
        'compras' => $compras ?: 0,
        'ganado' => $ganado,
        'cultivos' => $cultivos,
        'tareas' => $tareas
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
}
