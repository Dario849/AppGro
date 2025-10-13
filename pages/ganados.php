<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'GanadoS UwU', uid: $_SESSION['user_id']);
require dirname(__DIR__, 1) . '/system/resources/database.php';

if (!isset($_GET['id_grupo'])) {
    echo "Grupo no especificado.";
    exit;
}

$id_grupo = $_GET['id_grupo'];
$nro_caravana = isset($_GET['nro_caravana']) ? trim($_GET['nro_caravana']) : '';
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($nro_caravana !== '' && $nro_caravana !== '0') {
        // Busqueda por número de caravana
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