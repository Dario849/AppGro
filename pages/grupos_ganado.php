<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'Grupos_ganado UwU', uid: $_SESSION['user_id']);
require dirname(__DIR__, 2) .'\system\resources\database.php';
$conn = new mysqli('localhost', 'root', '', 'app_campo');
//require dirname(__DIR__,2) .'\system\ganados\Bganados.php';
if (!isset($_GET['id_grupo'])) {
    // echo "Grupo no especificado.";
    // exit;
}

$id_grupo = $_GET['id_grupo'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_vacuna'])) {
    $nombre_vacuna = $_POST['nombre_vacuna'];
    $proveedor_vacuna = $_POST['proveedor_vacuna'];
    $stmt = $conn->prepare("INSERT INTO vacunas (nombre_vacuna, proveedor) VALUES (?, ?)");
    $stmt->bind_param("ss", $nombre_vacuna, $proveedor_vacuna);
    if ($stmt->execute()) {
        $success_message = "Vacuna agregada correctamente.";
    } else {
        $error_message = "Error al agregar la vacuna: " . $conn->error;
    }
    $stmt->close();
}

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
            <!-- Form to add a new VACUNA -->
                <div style="margin-bottom: 20px;">
                    <h3>Agregar Nueva Vacuna</h3>
                    <form action="/grupos_ganado" method="POST">
                        <input type="text" name="nombre_vacuna" placeholder="Nombre de la vacuna" required>
                        <input type="text" name="proveedor_vacuna" placeholder="Proveedor de la vacuna" required>
                        <button type="submit" name="nueva_vacuna">Agregar Vacuna</button>
                    </form>

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

                    if ($conn->connect_error) {
                        die("Conexión fallida: " . $conn->connect_error);
                    }
                    if ($id_grupo) {
                        $sql = "SELECT SELECT g.id, g.fecha_desde, g.fecha_hasta, l.nombre , g.comentario 
                        FROM grupos g 
                        JOIN lotes l 
                        ON g.id_lote = l.id 
                        WHERE id = $id_grupo";
                    } else {
                        $sql = "SELECT g.id, g.fecha_desde, g.fecha_hasta, l.nombre , g.comentario 
                        FROM grupos g 
                        JOIN lotes l 
                        ON g.id_lote = l.id 
                        ORDER BY g.id ";
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