<?php
require dirname(__DIR__, 2) .'\system\resources\database.php';

if (!isset($_GET['id'])) {
    echo "Error: ID no recibido";
    exit;
}

$id_ganado = (int) $_GET['id'];

$conn = new mysqli('localhost', 'root', '', 'app_campo');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$stmt = $conn->prepare("
    SELECT v.nombre_vacuna, v.proveedor, gs.fecha_estado
    FROM ganado_sanidad gs
    JOIN vacunas v ON v.id = gs.id_vacuna
    WHERE gs.id_ganado = ?
    ORDER BY gs.fecha_estado DESC
");
$stmt->bind_param("i", $id_ganado);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No hay registros de vacunas</p>";
    exit;
}

echo "<table border='1' width='100%' cellpadding='5' cellspacing='0'>
        <tr>
            <th>Nombre</th>
            <th>Proveedor</th>
            <th>Fecha</th>
        </tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>".htmlspecialchars($row['nombre_vacuna'])."</td>
            <td>".htmlspecialchars($row['proveedor'])."</td>
            <td>".htmlspecialchars($row['fecha_estado'])."</td>
          </tr>";
}
echo "</table>";

$conn->close();
