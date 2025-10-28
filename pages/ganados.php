<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'GanadoS UwU', uid: $_SESSION['user_id']);
require dirname(__DIR__, 2) . '/system/resources/database.php';
$pdo = DB::connect();

$id_grupo = $_GET['id_grupo'] ?? null;
$nro_caravana = isset($_GET['nro_caravana']) ? trim($_GET['nro_caravana']) : '';

$success_message = '';
$error_message = '';

// --- Manejo del POST para agregar dieta ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_alimento'])) {
    $id_grupoForAlimento = $_POST['id_grupo_alimento'] ?? null;
    $id_alimento = $_POST['select_alimento'];

    if ($id_grupoForAlimento && $id_alimento) {
        try {
            $stmt = $pdo->prepare("INSERT INTO ganado_dietas (id_grupo, id_alimento, fecha_estado) VALUES (?, ?, NOW())");
            if ($stmt->execute([$id_grupoForAlimento, $id_alimento])) {
                header("Location: /ganados?id_grupo=" . urlencode($id_grupoForAlimento) . "&success=dieta_agregada");
                exit;
            } else {
                $error_message = "Error al agregar la dieta.";
            }
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } else {
        $error_message = "Faltan datos para agregar la dieta.";
    }
}

// --- Manejo del POST para agregar vacunas ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_vacuna_grupo'])) {
    $id_vacuna = $_POST['select_vacuna'] ?? null;
    $animales_seleccionados = $_POST['animales'] ?? [];
    $id_grupo_vacuna = $_POST['id_grupo_vacuna'] ?? null;

    if ($id_vacuna && $id_grupo_vacuna && !empty($animales_seleccionados)) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO ganado_sanidad (id_ganado, id_vacuna, fecha_estado) VALUES (?, ?, NOW())");

            foreach ($animales_seleccionados as $id_ganado) {
                $stmt->execute([$id_ganado, $id_vacuna]);
            }

            $pdo->commit();
            header("Location: /ganados?id_grupo=" . urlencode($id_grupo_vacuna) . "&success=vacuna_agregada");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Error al agregar vacunas: " . $e->getMessage();
        }
    } else {
        $error_message = "Faltan datos para agregar las vacunas.";
    }
}

// --- Mostrar mensaje de éxito desde URL ---
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'dieta_agregada') {
        $success_message = "Dieta agregada correctamente.";
    } elseif ($_GET['success'] === 'vacuna_agregada') {
        $success_message = "Vacunas agregadas correctamente.";
    }
}

// --- Cargar datos ---
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch animals for the group with debug
    $result = [];
    if ($id_grupo) {
        $stmt = $pdo->prepare("
            SELECT g.id, g.nro_caravana, g.id_tipo_ganado, g.sexo, g.fecha_nacimiento
            FROM ganado g
            INNER JOIN grupos_ganado gg ON g.id = gg.id_ganado
            WHERE gg.id_grupo = ?
        ");
        $stmt->execute([$id_grupo]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Debug: Check if animals are fetched
        if (empty($result)) {
            // Uncomment the next line to debug (remove after testing)
            // error_log("No animals found for id_grupo: $id_grupo");
        }
    }

    // Alimentos disponibles
    $alimentos_result = $pdo->query("SELECT id, nombre, marca FROM alimentos");

    // Vacunas disponibles
    $vacunas_result = $pdo->query("SELECT id, nombre_vacuna, proveedor FROM vacunas");

    // Última dieta del grupo
    $latest_diet = null;
    if ($id_grupo) {
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
    }

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

                <!-- Botón para abrir el modal de vacunas -->
                <div style="margin-bottom: 20px;">
                    <button type="button" onclick="abrirModalVacunas()" style="padding: 10px 20px; font-size: 16px;">Asignar Vacunas al Grupo</button>
                </div>

                <!-- Modal para asignar vacunas -->
                <div id="modalVacunas" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
                    <div style="background:#fff; padding:20px; border-radius:10px; width:700px; max-height:90%; overflow:auto; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                        <h2 style="text-align:center; margin-bottom:20px; border-bottom:1px solid #ddd; padding-bottom:10px;">Asignar Vacunas</h2>
                        <form action="/ganados" method="POST" id="vacunaForm">
                            <input type="hidden" name="id_grupo_vacuna" value="<?php echo htmlspecialchars($id_grupo); ?>">
                            <div style="margin-bottom:20px;">
                                <label for="select_vacuna" style="font-weight:bold; display:block; margin-bottom:5px;">Seleccionar Vacuna:</label>
                                <select name="select_vacuna" id="select_vacuna" required style="width:100%; padding:8px; font-size:16px; border:1px solid #ccc; border-radius:4px;">
                                    <option value="">Seleccione una vacuna</option>
                                    <?php
                                    $vacunas = $vacunas_result->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($vacunas as $vacuna) {
                                        echo "<option value='" . htmlspecialchars($vacuna['id']) . "'>"
                                            . htmlspecialchars($vacuna['nombre_vacuna']) . " (" . htmlspecialchars($vacuna['proveedor']) . ")</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div style="margin-bottom:20px;">
                                <label style="font-weight:bold; display:block; margin-bottom:10px;">Seleccionar Animales:</label>
                                <div style="max-height:400px; overflow-y:auto; border:1px solid #ddd; padding:10px; background:#f9f9f9;">
                                    <?php
                                    if ($id_grupo && !empty($result)) {
                                        foreach ($result as $animal) {
                                            echo "<label style='display:flex; align-items:center; margin:10px 0; padding:5px; border-bottom:1px solid #eee;'>";
                                            echo "<input type='checkbox' name='animales[]' value='" . htmlspecialchars($animal['id']) . "' style='margin-right:10px;'>";
                                            echo "<span style='font-size:16px;'>N° Caravana: " . htmlspecialchars($animal['nro_caravana']) . "</span></label>";
                                        }
                                    } else {
                                        echo "<p style='text-align:center; color:#666;'>No hay animales en este grupo.</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <button type="button" onclick="cerrarModalVacunas()" style="padding:10px 20px; margin-right:10px; background:#ccc; border:none; border-radius:4px; cursor:pointer;">Cancelar</button>
                                <button type="submit" name="nueva_vacuna_grupo" style="padding:10px 20px; background:#4CAF50; color:white; border:none; border-radius:4px; cursor:pointer;">Asignar Vacunas</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Formulario para agregar dieta -->
                <div style="margin-bottom: 20px;">
                    <h3>Agregar Nueva Dieta al Grupo</h3>
                    <form action="/ganados" method="POST">
                        <input type="hidden" name="id_grupo_alimento" value="<?php echo htmlspecialchars($id_grupo); ?>">
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

                    <!-- Mensajes -->
                    <?php if ($success_message): ?>
                        <p style="color: green; margin-top: 10px;"><?php echo htmlspecialchars($success_message); ?></p>
                    <?php endif; ?>
                    <?php if ($error_message): ?>
                        <p style="color: red; margin-top: 10px;"><?php echo htmlspecialchars($error_message); ?></p>
                    <?php endif; ?>

                    <!-- Última dieta -->
                    <?php if ($latest_diet): ?>
                        <p style="margin-top: 10px;">
                            <strong>Última Dieta:</strong>
                            <?php echo htmlspecialchars($latest_diet['nombre']) . " (" . htmlspecialchars($latest_diet['marca']) . ") - " . $latest_diet['fecha_estado']; ?>
                        </p>
                    <?php else: ?>
                        <p style="margin-top: 10px;">No hay dietas asignadas a este grupo.</p>
                    <?php endif; ?>
                </div>

                <!-- Tabla de animales -->
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
                        ?>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
</main>

<script>
function abrirModalVacunas() {
    const modal = document.getElementById("modalVacunas");
    modal.style.display = "flex";
}

function cerrarModalVacunas() {
    const modal = document.getElementById("modalVacunas");
    modal.style.display = "none";
}
</script>