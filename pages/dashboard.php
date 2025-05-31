<?php
require('system/main.php');
renderNavbar();

// Ajusta la ruta según corresponda a tu estructura real:
require_once __DIR__ . '/../../system/database.php';

$layout = new HTML(title: 'PHP via Vite');

// Usa siempre $conn
$db   = new Database();
$conn = $db->connect();

$sql    = "SELECT nombre FROM Usuarios WHERE id BETWEEN 1 AND 5";
$result = $conn->query($sql);

if ($result === false) {
    die("Error en la consulta: " . $conn->error);
}
?>

<main class="main__content">
    <div class="main_container">
        <div class="main_containerTiempo">
            <?php
            // 5) Recorremos y mostramos los nombres
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // escapamos por seguridad
                    $nombre = htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8');
                    echo $nombre;
                }
            } else {
                echo '<div class="text-gray-500">No se encontraron usuarios.</div>';
            }
            ?>
        </div>
        <div class="main_containerTareas">

        </div>
        <div class="main_containerMapa">

        </div>
    </div>
</main>
<script src="/src/scripts/repos.ts" type="module"></script>