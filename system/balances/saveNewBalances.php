<?php
header('Content-Type: application/json');
require __DIR__ . '/../resources/database.php';   // conexión PDO
$dateToSave = $_GET['fecha'] ?? null;
$valueToSave = $_GET['monto'] ?? null;
$typeToSave = $_GET['tipo'] ?? null;
$uIdToSave = $_GET['uid'] ?? null;
$productToSave = $_GET['prod'] ?? null;
$amountToSave = $_GET['prod_num'] ?? null;
$commentToSave = $_GET['detail'] ?? null;
try {
    // $getUser = "SELECT nombre, apellido FROM `usuarios`  WHERE id=?";
    // $stmt = $pdo->prepare($getUser);
    // $stmt->execute([$uIdToSave]);
    // $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // $user = $user['nombre'].'-'.$user['apellido'];
    $sql = "INSERT INTO `transacciones` (`fecha`, `monto`, `tipo`, `id_usuario`, `dato`, `dato_cantidad`, `detalle`) VALUES (?,?,?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dateToSave, $valueToSave, $typeToSave, $uIdToSave, $productToSave, $amountToSave, $commentToSave], );
    echo json_encode('Operación exitosa');
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
}