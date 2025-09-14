<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'Grupos_ganado UwU');
require dirname(__DIR__, 2) .'\system\resources\database.php';
//require dirname(__DIR__,2) .'\system\ganados\Bganados.php';

if (!isset($_GET['id_grupo'])) {
    // echo "Grupo no especificado.";
    // exit;
}

$id_grupo = $_GET['id_grupo'];

?>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerbuscador">
            <form action="/grupos_ganado" method="GET">
                <input type="text" name="id_grupo" placeholder="Buscar por ID del grupo de animales" required>
                <button type="submit">Buscar</button>
            </form>

        </div>
        <div class="main_containerganados">

        <fieldset>
            <legend>Datos del Animal</legend>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha Desde</th>
                        <th>Fecha Hasta</th>
                        <th>ID Subdivisión</th>
                        <th>Comentario</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Conexión a la base de datos
                    $conn = new mysqli('localhost', 'root', '', 'app_campo');

                    if ($conn->connect_error) {
                        die("Conexión fallida: " . $conn->connect_error);
                    }
                    if ($id_grupo) {
                        $sql = "SELECT id, fecha_desde, fecha_hasta, id_subdivision, comentario 
                                FROM grupos 
                                WHERE id = $id_grupo";
                    } else {
                        $sql = "SELECT id, fecha_desde, fecha_hasta, id_subdivision, comentario FROM grupos";
                    }

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($grupo = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($grupo['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($grupo['fecha_desde']) . "</td>";
                            echo "<td>" . htmlspecialchars($grupo['fecha_hasta']) . "</td>";
                            echo "<td>" . htmlspecialchars($grupo['id_subdivision']) . "</td>";
                            echo "<td>" . htmlspecialchars($grupo['comentario']) . "</td>";
                            echo "<td><a href='/ganados?id_grupo=" . urlencode($grupo['id']) . "'>Ver animales</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No hay registros disponibles</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </fieldset>
    </div>
</div>
</main>