<?php
header('Content-Type: application/json');
require __DIR__ . '/../resources/database.php';   // conexión PDO
$dateToSave = $_GET['fecha']??null;
$valueToSave = $_GET['monto']??null;
$typeToSave = $_GET['tipo']??null;
try {
    $sql = "INSERT INTO `transacciones` (`fecha`, `monto`, `tipo`) VALUES (?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dateToSave, $valueToSave, $typeToSave]);
    echo json_encode('Operación exitosa');
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
}