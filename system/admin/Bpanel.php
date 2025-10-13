<?php
header('Content-Type: application/json');
require __DIR__ . '/../resources/database.php';   // conexión PDO
$uid = $_POST['uid'] ?? null;
$newEstado = $_POST['estado'] ?? null;
// Obtener usuarios
try {
    if ($uid != null && $newEstado == null) {//Trae información del usuario, carga lista de vistas a disposición.
        $usuario_stmt = $pdo->prepare("SELECT id, estado, nombre, apellido, username, fecha_nacimiento, TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad FROM usuarios WHERE id = ? AND estado = 'activo' ORDER BY id");
        $usuario_stmt->execute([$uid]);
        $datos = $usuario_stmt->fetch(PDO::FETCH_ASSOC);
        $vistas_stmt = $pdo->prepare("SELECT nombre, id FROM vistas ORDER BY nombre");
        $vistas_stmt->execute();
        $vistas = $vistas_stmt->fetchAll(PDO::FETCH_ASSOC);
        $vistas_usuario_stmt = $pdo->prepare("SELECT v.nombre FROM vistas v 
                                   JOIN usuarios_vistas uv ON uv.id_vista = v.id
                                   WHERE uv.id_usuario = ?");
        $vistas_usuario_stmt->execute([$uid]);
        $permisos = $vistas_usuario_stmt->fetchAll(PDO::FETCH_COLUMN);
        echo json_encode(['datos' => $datos, 'vistas' => $vistas, 'permisos' => $permisos]);
    } elseif ($uid != null && $newEstado != null) {
        $update_stmt = $pdo->prepare("UPDATE usuarios SET estado = ? WHERE id = ?");
        $ok = $update_stmt->execute([$newEstado, $uid]);

        if ($ok && $update_stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => "Usuario actualizado correctamente",
                'uid' => $uid,
                'estado' => $newEstado
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => "No se pudo actualizar el usuario o no hubo cambios",
                'uid' => $uid
            ]);
        }
    } else { //Trae lista de usuarios        
        $stmt = $pdo->query("SELECT id AS id_usuario, nombre, apellido, estado FROM usuarios ORDER BY nombre");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($usuarios);
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
