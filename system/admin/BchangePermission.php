<?php
require __DIR__ . '/../resources/database.php';   // conexión PDO
$selectedUserId = intval($_POST['selectedUserId']); //ID del usuario a relacionar con ID de vista
$permissionId = intval($_POST['permId']); //ID de la vista
if (!empty($permissionId) && !empty($selectedUserId)) {
    if ($selectedUserId || $permissionId) {//Existe solicitúd de cambio de permisos
        echo "Recibido User ID: ".$selectedUserId." y perm ID: ".$permissionId;
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // 1. Verificar si ya existe la relación
        $checkStmt = $pdo->prepare("SELECT id AS id_us_vista FROM usuarios_vistas WHERE id_usuario = ? AND id_vista = ?");
        $checkStmt->execute([$selectedUserId, $permissionId]);
        $exists = $checkStmt->fetch(PDO::FETCH_ASSOC);
        if ($exists) {
            // 2. Si existe → eliminar (desactivar permiso)
            $id_vista = $exists['id_us_vista'];
            $deleteStmt = $pdo->prepare("DELETE FROM usuarios_vistas WHERE id = ? ");
            $deleteStmt->execute([$id_vista]);
            echo json_encode(["status" => "removed", "message" => "Permiso desactivado"]);
        } else {
            // 3. Si no existe → insertar (activar permiso)
            $insertStmt = $pdo->prepare("INSERT INTO usuarios_vistas (id_usuario, id_vista) VALUES (?, ?)");
            $insertStmt->execute([$selectedUserId, $permissionId]);
            echo json_encode(["status" => "added", "message" => "Permiso activado"]);
        }
    }
} else {
    echo "No se ha recibido request/variables";
}
?>