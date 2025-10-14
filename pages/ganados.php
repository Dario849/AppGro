<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'GanadoS UwU', uid: $_SESSION['user_id']);
require dirname(__DIR__, 1) . '/system/resources/database.php';


$id_grupo = $_GET['id_grupo'] ?? null;
$nro_caravana = isset($_GET['nro_caravana']) ? trim($_GET['nro_caravana']) : '';
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $success_message = '';
    $error_message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_alimento'])) {
        $id_grupoForAlimento = $_POST['id_grupo_alimento'];
        $id_alimento = $_POST['select_alimento'];
        $stmt = $pdo->prepare("INSERT INTO ganado_dietas (id_grupo, id_alimento, fecha_estado) VALUES (?, ?, NOW())");
        if ($stmt->execute([$id_grupoForAlimento, $id_alimento])) {
            $success_message = "Dieta agregada correctamente.";
        } else {
            $error_message = "Error al agregar la dieta: " . implode(" ", $stmt->errorInfo());
        }
    }
    /*
PARA HACER VACUNAS SI VAN TODAS JUNTAS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_vacuna'])) {
    $id_vacuna = $_POST['select_vacuna'];
    $stmt = $conn->prepare("INSERT INTO ganado_sanidad (id_grupo, id_alimento, fecha_estado) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $id_grupoForAlimento, $id_alimento);
    if ($stmt->execute()) {
        $success_message = "Dieta agregada correctamente.";
    } else {
        $error_message = "Error al agregar la dieta: " . $conn->error;
    }
    $stmt->close();
}
*/
    // Fetch animals in the group
    if ($nro_caravana !== '' && $nro_caravana !== '0') {
        $stmt = $pdo->prepare("
            SELECT g.id, g.nro_caravana, g.id_tipo_ganado, g.sexo, g.fecha_nacimiento
            FROM ganado g
            INNER JOIN grupos_ganado gg ON g.id = gg.id_ganado
            WHERE gg.id_grupo = ? AND g.nro_caravana = ? ");
        $stmt->execute([$id_grupo, $nro_caravana]);
    } else {
        $stmt = $pdo->prepare("
            SELECT g.id, g.nro_caravana, g.id_tipo_ganado, g.sexo, g.fecha_nacimiento
            FROM ganado g
            INNER JOIN grupos_ganado gg ON g.id = gg.id_ganado
            WHERE gg.id_grupo = ?
        ");
        $stmt->execute([$id_grupo]);
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Fetch available foods for the dropdown
    $alimentos_result = $pdo->query("SELECT id, nombre, marca FROM alimentos");

    // Fetch the latest diet for the group
    $latest_diet = null;
    $stmt = $pdo->prepare("
    SELECT a.nombre, a.marca, gd.fecha_estado
    FROM ganado_dietas gd
    INNER JOIN alimentos a ON gd.id_alimento = a.id
    WHERE gd.id_grupo = ?
    ORDER BY gd.fecha_estado DESC
    LIMIT 1
");
    $stmt->execute([$id_grupo]);
    $latest_diet = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
    exit;
}
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
            <fieldset>
                <legend>Ganado del Grupo <?php echo htmlspecialchars($id_grupo); ?></legend>

                <!-- Form to add a new diet -->
                <div style="margin-bottom: 20px;">
                    <h3>Agregar Nueva Dieta al Grupo</h3>
                    <form action="/ganados" method="POST">
                        <input type="hidden" name="id_grupo_alimento"
                            value="<?php echo htmlspecialchars($id_grupo); ?>">
                        <label for="id_alimento">Seleccionar Alimento:</label>
                        <select name="select_alimento" id="id_alimento" required>
                            <option value="">Seleccione un alimento</option>
                            <?php
                            $alimentos = $alimentos_result->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($alimentos as $alimento) {
                                echo "<option value='" . htmlspecialchars($alimento['id']) . "'>"
                                    . htmlspecialchars($alimento['nombre']) . " (" . htmlspecialchars($alimento['marca']) . ")</option>";
                            }
                            ?>

                        </select>
                        <button type="submit">Agregar Dieta</button>
                    </form>

                    <!-- Display success or error message -->
                    <?php if ($success_message): ?>
                        <p style="color: green; margin-top: 10px;"><?php echo htmlspecialchars($success_message); ?></p>
                    <?php endif; ?>
                    <?php if ($error_message): ?>
                        <p style="color: red; margin-top: 10px;"><?php echo htmlspecialchars($error_message); ?></p>
                    <?php endif; ?>

                    <!-- Display latest diet -->
                    <?php if ($latest_diet): ?>
                        <p style="margin-top: 10px;">
                            <strong>Última Dieta:</strong>
                            <?php echo htmlspecialchars($latest_diet['nombre']) . " (" . htmlspecialchars($latest_diet['marca']) . ")"; ?>
                        </p>
                    <?php else: ?>
                        <p style="margin-top: 10px;">No hay dietas asignadas a este grupo.</p>
                    <?php endif; ?>
                </div>

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
                        if (count($result) > 0) {
                            foreach ($result as $animal) {
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
                        $stmt = null;
                        $pdo = null;
                        ?>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
</main>