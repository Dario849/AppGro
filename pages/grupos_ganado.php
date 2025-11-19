<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'Grupos_ganado UwU', uid: $_SESSION['user_id']);
require dirname(__DIR__, 2) . '/system/resources/database.php';
$pdo = DB::connect();

$id_grupo = $_GET['id_grupo'] ?? null;

$success_message = '';
$error_message = '';

// --- Manejo del POST para nuevo grupo ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_grupo'])) {
    $id_lote     = $_POST['id_lote'] ?? null;
    $fecha_desde = $_POST['fecha_desde'] ?? null;
    $fecha_hasta = !empty($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;
    $comentario  = $_POST['comentario'] ?? '';

    if ($id_lote && $fecha_desde) {
        try {
            $stmt = $pdo->prepare("INSERT INTO grupos (id_lote, fecha_desde, fecha_hasta, comentario) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$id_lote, $fecha_desde, $fecha_hasta, $comentario])) {
                $success_message = "Grupo agregado correctamente.";
            } else {
                $error_message = "Error al agregar el grupo: " . implode(" ", $stmt->errorInfo());
            }
        } catch (PDOException $e) {
            $error_message = "Error al agregar el grupo: " . $e->getMessage();
        }
    } else {
        $error_message = "Faltan datos requeridos para agregar el grupo (lote y fecha desde).";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_vacuna'])) {
    $nombre_vacuna    = $_POST['nombre_vacuna'];
    $proveedor_vacuna = $_POST['proveedor_vacuna'];

    $stmt = $pdo->prepare("INSERT INTO vacunas (nombre_vacuna, proveedor) VALUES (?, ?)");
    if ($stmt->execute([$nombre_vacuna, $proveedor_vacuna])) {
        $success_message = "Vacuna agregada correctamente.";
    } else {
        $error_message = "Error al agregar la vacuna: " . implode(" ", $stmt->errorInfo());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_alimento'])) {
    $nombre_alimento   = $_POST['nombre_alimento'];
    $marca_alimento    = $_POST['marca_alimento'];
    $id_tipo_alimento  = $_POST['id_tipo_alimento'];

    $stmt = $pdo->prepare("INSERT INTO alimentos (nombre, marca, id_tipo_alimento) VALUES (?, ?, ?)");
    if ($stmt->execute([$nombre_alimento, $marca_alimento, $id_tipo_alimento])) {
        $success_message = "Alimento agregado correctamente.";
    } else {
        $error_message = "Error al agregar el alimento: " . implode(" ", $stmt->errorInfo());
    }
}

// Lotes para el modal
$lotes_result = $pdo->query("SELECT id, nombre FROM lotes");
$lotes = $lotes_result->fetchAll(PDO::FETCH_ASSOC);
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
            <!-- Botón para agregar nuevo grupo -->
            <div style="margin-bottom: 20px;">
                <button type="button" onclick="abrirModalNuevoGrupo()">Agregar Nuevo Grupo</button>
            </div>

            <!-- Formularios de vacuna y alimento (sin cambios) -->
            <div style="margin-bottom: 20px;">
                <h3>Agregar Nueva Vacuna</h3>
                <form action="/grupos_ganado" method="POST">
                    <input type="text" name="nombre_vacuna" placeholder="Nombre de la vacuna" required>
                    <input type="text" name="proveedor_vacuna" placeholder="Proveedor de la vacuna" required>
                    <button type="submit" name="nueva_vacuna">Agregar Vacuna</button>
                </form>

                <h3>Agregar Nuevo Alimento</h3>
                <form action="/grupos_ganado" method="POST">
                    <input type="text" name="nombre_alimento" placeholder="Nombre del alimento" required>
                    <input type="text" name="marca_alimento" placeholder="Marca del alimento" required>
                    <select name="id_tipo_alimento" required>
                        <option value="">Seleccione un tipo de alimento</option>
                        <?php
                        $tipos = $pdo->query("SELECT id, nombre FROM tipos_alimento")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($tipos as $t) {
                            echo "<option value='" . htmlspecialchars($t['id']) . "'>" . htmlspecialchars($t['nombre']) . "</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" name="nuevo_alimento">Agregar Alimento</button>
                </form>
            </div>

            <!-- Mensajes -->
            <?php if ($success_message): ?>
                <p style="color:green; margin-top:10px;"><?= htmlspecialchars($success_message) ?></p>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <p style="color:red; margin-top:10px;"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>

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
                        try {
                            // Si se busca un grupo específico
                            if ($id_grupo) {
                                $sql = "SELECT g.id, g.fecha_desde, g.fecha_hasta, l.nombre, g.comentario 
                                        FROM grupos g 
                                        JOIN lotes l ON g.id_lote = l.id 
                                        WHERE g.id = ?";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute([$id_grupo]);
                            } else {
                                // Orden: primero los activos (fecha_hasta NULL), luego los cerrados
                                $sql = "SELECT g.id, g.fecha_desde, g.fecha_hasta, l.nombre, g.comentario 
                                        FROM grupos g 
                                        JOIN lotes l ON g.id_lote = l.id 
                                        ORDER BY 
                                            g.fecha_hasta IS NULL DESC,   -- activos primero
                                            g.fecha_hasta DESC,          -- luego los más recientes cerrados
                                            g.id DESC";
                                $stmt = $pdo->query($sql);
                            }

                            $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($grupos) {
                                foreach ($grupos as $grupo) {
                                    $desde = $grupo['fecha_desde'] ? date('d-m-Y', strtotime($grupo['fecha_desde'])) : '—';
                                    $hasta = $grupo['fecha_hasta'] 
                                        ? date('d-m-Y', strtotime($grupo['fecha_hasta'])) 
                                        : '<span style="color:#28a745;font-weight:bold;">ACTIVO</span>';

                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($grupo['id']) . "</td>";
                                    echo "<td>$desde</td>";
                                    echo "<td>$hasta</td>";
                                    echo "<td>" . htmlspecialchars($grupo['nombre']) . "</td>";
                                    echo "<td>" . htmlspecialchars($grupo['comentario'] ?? '') . "</td>";
                                    echo "<td><a href='/ganados?id_grupo=" . urlencode($grupo['id']) . "'>Ver animales</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No hay registros disponibles</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='6'>Error: " . $e->getMessage() . "</td></tr>";
                        }
                        $pdo = null;
                        ?>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
</main>

<!-- Modal para agregar nuevo grupo (sin cambios) -->
<div id="modalNuevoGrupo" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div style="background:#fff; padding:15px; border-radius:8px; width:80%; max-width:400px; box-shadow:0 4px 15px rgba(0,0,0,0.2);">
        <h3 style="margin:0 0 10px; text-align:center;">Nuevo Grupo</h3>
        <form action="/grupos_ganado" method="POST">
            <select name="id_lote" required>
                <option value="">Lote</option>
                <?php foreach ($lotes as $lote): ?>
                    <option value="<?= htmlspecialchars($lote['id']) ?>"><?= htmlspecialchars($lote['nombre']) ?></option>
                <?php endforeach; ?>
            </select><br><br>
            <input type="date" name="fecha_desde" required><br><br>
            <input type="date" name="fecha_hasta" placeholder="Fecha Hasta (opcional)"><br><br>
            <textarea name="comentario" rows="2" placeholder="Comentario (opcional)"></textarea><br><br>
            <button type="submit" name="nuevo_grupo">Agregar</button>
            <button type="button" onclick="cerrarModalNuevoGrupo()">Cancelar</button>
        </form>
    </div>
</div>

<script>
function abrirModalNuevoGrupo() {
    document.getElementById("modalNuevoGrupo").style.display = "flex";
}
function cerrarModalNuevoGrupo() {
    document.getElementById("modalNuevoGrupo").style.display = "none";
}
</script>