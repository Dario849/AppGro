<?php
header('Content-Type: application/json');
require __DIR__ . '/../resources/database.php';   // conexión PDO
$uid = $_POST['uid'] ?? null;
// Obtener usuarios
try {
    if ($uid!=null) {//Trae información del usuario, carga lista de vistas a disposición.
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
    }else{ //Trae lista de usuarios        
        $stmt = $pdo->query("SELECT id AS id_usuario, nombre FROM usuarios  WHERE estado = 'activo' ORDER BY nombre");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($usuarios);
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
