<?php
header("Content-Type: text/html; charset=utf-8");

$criterio = $_POST['criterio'] ?? '';

if (!$criterio || $criterio === 'ninguno') {
  echo "<p>Error: Falta criterio válido.</p>";
  echo '<a href="tareas.php">Volver</a>';
  exit;
}

switch ($criterio) {
  case 'semanal': $dias = 7; break;
  case 'mensual': $dias = 30; break;
  case 'anual': $dias = 365; break;
  default:
    echo "<p>Error: Criterio inválido.</p>";
    echo '<a href="tareas.php">Volver</a>';
    exit;
}

$limite = date('Y-m-d', strtotime("-$dias days"));

$conn = new mysqli("localhost", "root", "", "app_campo");
if ($conn->connect_error) {
  echo "<p>Error de conexión: {$conn->connect_error}</p>";
  exit;
}

$stmt = $conn->prepare("UPDATE Tareas SET baja_logica = 1 WHERE (estado = 'completada' OR estado = 'cancelada') AND DATE(fecha_hora_fin) < ?");
$stmt->bind_param("s", $limite);
$stmt->execute();

$filas = $stmt->affected_rows;

$stmt->close();
$conn->close();

echo "<p>Se eliminaron $filas tareas.</p>";
echo '<a href="tareas.php"></a>';
exit;
?>
