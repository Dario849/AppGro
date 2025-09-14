<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'GanadoS UwU');
require dirname(__DIR__, 2) .'\system\resources\database.php';

if (!isset($_GET['id_grupo'])) {
    echo "Grupo no especificado.";
    exit;
}

$id_grupo = $_GET['id_grupo'];
$nro_caravana = isset($_GET['nro_caravana']) ? trim($_GET['nro_caravana']) : '';

$conn = new mysqli('localhost', 'root', '', 'app_campo');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
if ($nro_caravana !== '' && $nro_caravana !== '0') {
    // Busqueda por número de caravana
    $stmt = $conn->prepare("
        SELECT g.id, g.nro_caravana, g.id_tipo_ganado, g.sexo, g.fecha_nacimiento
        FROM ganado g
        INNER JOIN grupos_ganado gg ON g.id = gg.id_ganado
        WHERE gg.id_grupo = ? AND g.nro_caravana = ?
    ");
    $stmt->bind_param("is", $id_grupo, $nro_caravana);
} else {
    $stmt = $conn->prepare("
        SELECT g.id, g.nro_caravana, g.id_tipo_ganado, g.sexo, g.fecha_nacimiento
        FROM ganado g
        INNER JOIN grupos_ganado gg ON g.id = gg.id_ganado
        WHERE gg.id_grupo = ?
    ");
    $stmt->bind_param("i", $id_grupo);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="main__content">
    <div class="main_container">
        <div class="main_containerbuscador" style="display: flex; justify-content: space-around;">
            <form action="/ganados" method="GET">
                <input type="text" name="nro_caravana" placeholder="Buscar por numero de caravana del animal">
                <input type="hidden" name="id_grupo" value="<?php echo htmlspecialchars($id_grupo); ?>">
                <button type="submit">Buscar</button>
            </form>
            <form action="/grupos_ganado" method="GET">
                <button type="submit">Volver a los grupos</button>
            </form>

        </div>
        <div class="main_containerganados">
            <title>Ganado del Grupo <?php echo htmlspecialchars($id_grupo); ?></title>
        <fieldset>
            <legend>Ganado del Grupo <?php echo htmlspecialchars($id_grupo); ?></legend>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>N° Caravana</th>
                        <th>Tipo</th>
                        <th>Sexo</th>
                        <th>Fecha Nacimiento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($animal = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($animal['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($animal['nro_caravana']) . "</td>";
                            echo "<td>" . htmlspecialchars($animal['id_tipo_ganado']) . "</td>";
                            echo "<td>" . htmlspecialchars($animal['sexo']) . "</td>";
                            echo "<td>" . htmlspecialchars($animal['fecha_nacimiento']) . "</td>";
                            echo "<td><a href='/ganado?nro_caravana=" . urlencode($animal['nro_caravana']) . "'>Ver detalles</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>Este grupo no tiene animales.</td></tr>";
                    }
                    $stmt->close();
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </fieldset>
        </div>
    </div>
</main>