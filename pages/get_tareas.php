<?php
header('Content-Type: application/json');

require dirname(__DIR__, 2) .'\system\resources\database.php';

// if ($conn->connect_error) { SE CAMBIA POR UN SIMPLE TRY CATCH YA QUE PDO LANZA EXCEPCION
//     http_response_code(500);
//     echo json_encode(["error" => "Error de conexión"]);
//     exit;
// }

$estado = $_GET['estado'] ?? 'todas';
$orden = $_GET['orden'] ?? '';
$direccion = strtolower($_GET['direccion'] ?? 'asc');

// NUEVO: Parámetros para filtrar por mes y año
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : 0;
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : 0;

// Validación dirección segura || VALIDACION INNECESARIA, SI ES NULL, VA A SER 'ASC'
// if (!in_array($direccion, ['asc', 'desc'])) {
//     $direccion = 'asc';
//     echo (var_dump($direccion));
// }

// Validación estado || VALIDACION INNECESARIA, SI ES NULL, VA A SER 'TODAS'
// $estados_permitidos = ['activa', 'completada', 'cancelada', 'todas'];
// if (!in_array($estado, $estados_permitidos)) {
//     http_response_code(400);
//     echo json_encode(["error" => "Estado inválido"]);
//     exit;
// }

try{
$sql = "SELECT * FROM tareas WHERE baja_logica = 0";
$params = [];
$types = "";

// Filtro por estado (si no es todas)
if ($estado !== 'todas') {
    $sql .= " AND estado = ?";
    $params[] = $estado;
    $types .= "s";
}

// NUEVO: filtro por mes y año si están seteados y válidos
if ($mes > 0 && $anio > 0) {
    $fechaInicio = "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-01";
    $fechaFin = date("Y-m-t", strtotime($fechaInicio));

    $sql .= " AND fecha_hora_inicio BETWEEN ? AND ?";
    $params[] = $fechaInicio;
    $params[] = $fechaFin;
    $types .= "ss";
}

// Orden por vencimiento si corresponde
if ($orden === 'vencimiento') {
    $sql .= " ORDER BY fecha_hora_fin $direccion";
}

$stmt = $pdo->prepare($sql);

// Si hay parámetros, los vinculamos || NO ES NECESARIO, YA QUE NO HAY PARAMETROS QUE NO SEAN STRINGS
// if (!empty($params)) {
//     echo json_encode(var_dump($params) );
//     $stmt->bindParam($params,PDO::PARAM_STR);
// }

$stmt->execute($params??[]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$tareas = [];
// while ($row = $result->fetch_assoc()) { || NO ES NECESARIO, YA QUE FETCHALL DEVUELVE UN ARRAY ASOCIATIVO
//     $tareas[] = $row;
// }
foreach($result as $row){
    $tareas[] = $row;
}
// SE ELIMINARON OTRAS LINEAS DE CODIGO INNECESARIAS DEBIDO A QUE NO SON PROPIAS DE PDO
echo json_encode($tareas);
$pdo = null; // CIERRO LA CONEXION A LA BASE DE DATOS DE PDO
$stmt = null; // CIERRO LA CONEXION A LA BASE DE DATOS DE PDO
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
    exit;
}