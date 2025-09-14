<?php
header('Content-Type: application/json');
require __DIR__ . '/../resources/database.php';   // conexión PDO
try {
    $stmt = $pdo->query("SELECT * FROM transacciones ORDER BY fecha DESC LIMIT 100");
    $oldBalances = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($oldBalances);
    exit();
}catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
}