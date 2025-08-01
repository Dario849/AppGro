<?php
header('Content-Type: application/json');
require dirname(__DIR__,4) . '\system\resources\database.php';

// Activar logs visibles para debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

// === 1. Parámetros seguros ===
$tipo = $_GET['tipo'] ?? '';
$desde = $_GET['desde'] ?? '2000-01-01';
$hasta = $_GET['hasta'] ?? date('Y-m-d');
$grupo = $_GET['grupo'] ?? 'mes';

$parametros = [$desde, $hasta];

// === 2. Mapear tipo → tabla, campo_fecha ===
$mapa = [
    'ventas'         => ['tabla' => 'transacciones', 'campo' => 'fecha'],
    'compras'        => ['tabla' => 'transacciones', 'campo' => 'fecha'],
    'balance'        => ['tabla' => 'transacciones', 'campo' => 'fecha'],
    'ganado_alta'    => ['tabla' => 'ganado', 'campo' => 'fecha_nacimiento'],
    'cultivo_alta'   => ['tabla' => 'cultivos', 'campo' => 'fecha_siembra'],
    'tareas_completadas' => ['tabla' => 'tareas', 'campo' => 'fecha_hora_inicio'],
];

// === 3. Validación de tipo ===
if (!array_key_exists($tipo, $mapa)) {
    http_response_code(400);
    echo json_encode(['error' => 'Tipo inválido']);
    exit;
}

$tabla = $mapa[$tipo]['tabla'];
$columna_fecha = $mapa[$tipo]['campo'];

// === 4. Agrupamiento dinámico ===
switch ($grupo) {
    case 'anio': $expr_fecha = "YEAR($columna_fecha)"; break;
    case 'dia':  $expr_fecha = "DATE($columna_fecha)"; break;
    default:     $expr_fecha = "DATE_FORMAT($columna_fecha, '%Y-%m')";
}

// === 5. Construcción de SQL por tipo ===
switch ($tipo) {
    case 'ventas':
        $sql = "
            SELECT {$expr_fecha} AS periodo, SUM(monto) AS total
            FROM $tabla
            WHERE tipo = 'VENTA' AND $columna_fecha BETWEEN ? AND ?
            GROUP BY periodo ORDER BY periodo ASC
        ";
        break;

    case 'compras':
        $sql = "
            SELECT {$expr_fecha} AS periodo, SUM(monto) AS total
            FROM $tabla
            WHERE tipo = 'COMPRA' AND $columna_fecha BETWEEN ? AND ?
            GROUP BY periodo ORDER BY periodo ASC
        ";
        break;

    case 'balance':
        $sql = "
            SELECT 
                {$expr_fecha} AS periodo,
                SUM(CASE WHEN tipo = 'VENTA' THEN monto ELSE 0 END) AS ventas,
                SUM(CASE WHEN tipo = 'COMPRA' THEN monto ELSE 0 END) AS compras,
                SUM(CASE WHEN tipo = 'VENTA' THEN monto ELSE 0 END) -
                SUM(CASE WHEN tipo = 'COMPRA' THEN monto ELSE 0 END) AS balance
            FROM $tabla
            WHERE $columna_fecha BETWEEN ? AND ?
            GROUP BY periodo ORDER BY periodo ASC
        ";
        break;

    case 'ganado_alta':
    case 'cultivo_alta':
        $sql = "
            SELECT {$expr_fecha} AS periodo, COUNT(*) AS cantidad
            FROM $tabla
            WHERE $columna_fecha BETWEEN ? AND ?
            GROUP BY periodo ORDER BY periodo ASC
        ";
        break;

    case 'tareas_completadas':
        $sql = "
            SELECT {$expr_fecha} AS periodo, COUNT(*) AS completadas
            FROM tareas
            WHERE estado = 'completada' AND baja_logica = 0
              AND $columna_fecha BETWEEN ? AND ?
            GROUP BY periodo ORDER BY periodo ASC
        ";
        break;
}

// === 6. Ejecutar y devolver JSON ===
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en SQL: ' . $e->getMessage()]);
}