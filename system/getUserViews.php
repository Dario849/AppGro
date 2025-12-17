    <?php 
    header('Content-Type: application/json');
    session_start();
    require dirname(__DIR__) . '/system/resources/database.php'; // conexión PDO
    $pdo = DB::connect();
    $uid = $_SESSION['user_id'] ?? 0;
    $currentPerm = $pdo->prepare("SELECT v.nombre
    FROM usuarios_vistas uv
    JOIN vistas v ON uv.id_vista = v.id
    WHERE uv.id_usuario = ?
    ORDER BY v.id"  );
    $currentPerm->execute([intval($uid)]);
    $access = $currentPerm->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($access);