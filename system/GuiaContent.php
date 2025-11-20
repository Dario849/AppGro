<?php
header('Content-Type: application/json');
session_start();
require dirname(__DIR__) . '/system/resources/database.php'; // conexión PDO
$pdo = DB::connect();
$action = $_POST['action'] ?? null;
$content = $_POST['content'] ?? null;
$uid = $_SESSION['user_id'] ?? null;
try {
    switch ($action) {
        case 0:
        case 1:
            //Busca el registro
            $sql = 'select * from guia WHERE id = 1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($result);
            break;
        case 2:
            if ($uid < 1) {
                echo json_encode(['error' => 'Para modificar es necesario que inicie sesión primero']);
                break;
            } else {
                //Cambia el registro
                $pdo->beginTransaction();

                $stmt = $pdo->prepare('UPDATE guia SET contenido = ?, fecha_estado = NOW(), id_usuario_estado = ? WHERE id = 1');
                $stmt->execute([$content, $uid]);

                $stmt = $pdo->prepare('SELECT * FROM guia WHERE id = 1');
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                $pdo->commit();
                echo json_encode($result);
                break;
            }
        default:
            echo json_encode(['error' => 'No se especificó una acción']);
            break;
    }

} catch (\Throwable $th) {
    $pdo->rollBack();
    echo json_encode(['error' => $th->getMessage()]);
}
