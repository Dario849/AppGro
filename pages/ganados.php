<?php
require('system/main.php');
sessionCheck();
renderNavbar($_SESSION['user_id']);
$layout = new HTML(title: 'GanadoS UwU');
require dirname(__DIR__, 2) .'\system\resources\database.php';

if (!isset($_GET['id_grupo'])) {
    echo "Grupo no especificado.";
    exit;
}

$id_grupo = $_GET['id_grupo'];

$conn = new mysqli('localhost', 'root', '', 'app_campo');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$stmt = $conn->prepare("
    SELECT g.id, g.nro_caravana, g.id_tipo_ganado, g.sexo, g.fecha_nacimiento
    FROM ganado g
    INNER JOIN grupos_ganado gg ON g.id = gg.id_ganado
    WHERE gg.id_grupo = ?
");
$stmt->bind_param("i", $id_grupo);
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="main__content">
    <div class="main_container">
        <div class="main_containerganados">
            <title>Ganado del Grupo <?php echo htmlspecialchars($id_grupo); ?></title>
            <!-- <style>
                fieldset {
                    border: 2px solid #007bff;
                    border-radius: 10px;
                    background-color: #f0f0f0;
                    padding: 10px;
                    margin: 20px;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                }

                th, td {
                    padding: 8px;
                    border: 1px solid #ccc;
                }

                th {
                    background-color: #007bff;
                    color: white;
                }
            </style> -->
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
                            echo "<td><a href='/ganado?id=" . urlencode($animal['id']) . "'>Ver detalles</a></td>";
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