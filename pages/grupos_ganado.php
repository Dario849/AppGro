<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'Grupos_ganado UwU', uid: $_SESSION['user_id']);
require dirname(__DIR__, 2) . '/system/resources/database.php';
$pdo = DB::connect();


$id_grupo = $_GET['id_grupo'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_vacuna'])) {
    $nombre_vacuna = $_POST['nombre_vacuna'];
    $proveedor_vacuna = $_POST['proveedor_vacuna'];

    $stmt = $pdo->prepare("INSERT INTO vacunas (nombre_vacuna, proveedor) VALUES (?, ?)");
    if ($stmt->execute([$nombre_vacuna, $proveedor_vacuna])) {
        $success_message = "Vacuna agregada correctamente.";
    } else {
        $error_message = "Error al agregar la vacuna: " . implode(" ", $stmt->errorInfo());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_alimento'])) {
    $nombre_alimento = $_POST['nombre_alimento'];
    $marca_alimento = $_POST['marca_alimento'];
    $id_tipo_alimento = $_POST['id_tipo_alimento'];

    $stmt = $pdo->prepare("INSERT INTO alimentos (nombre, marca, id_tipo_alimento) VALUES (?, ?, ?)");
    if ($stmt->execute([$nombre_alimento, $marca_alimento, $id_tipo_alimento])) {
        $success_message = "Alimento agregado correctamente.";
    } else {
        $error_message = "Error al agregar el alimento: " . implode(" ", $stmt->errorInfo());
    }
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

                <!-- Form to add a new ALIMENTO -->
                <h3>Agregar Nuevo Alimento</h3>
                <form action="/grupos_ganado" method="POST">
                    <input type="text" name="nombre_alimento" placeholder="Nombre del alimento" required>
                    <input type="text" name="marca_alimento" placeholder="Marca del alimento" required>
                    <select name="id_tipo_alimento" id="id_tipo_alimento" required>
                        <option value="">Seleccione un tipo de alimento</option>
                        <?php
                        $tipos_alimento_result = $pdo->query("SELECT id, nombre FROM tipos_alimento");
                        $tipos_alimento = $tipos_alimento_result->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($tipos_alimento as $tipo_alimento) {
                            echo "<option value='" . htmlspecialchars($tipo_alimento['id']) . "'>"
                                . htmlspecialchars($tipo_alimento['nombre']) . "</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" name="nuevo_alimento">Agregar Alimento</button>
                </form>

                <!-- Display success or error message -->
                <?php if (isset($success_message)): ?>
                    <p style="color: green; margin-top: 10px;"><?php echo htmlspecialchars($success_message); ?></p>
                <?php endif; ?>
                <?php if (isset($error_message)): ?>
                    <p style="color: red; margin-top: 10px;"><?php echo htmlspecialchars($error_message); ?></p>
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
                            // Conexión a la base de datos
                            

                            try {
                                $stmt = $pdo->prepare(
                                    $id_grupo ?
                                    "SELECT g.id, g.fecha_desde, g.fecha_hasta, l.nombre , g.comentario 
                        FROM grupos g 
                        JOIN lotes l 
                        ON g.id_lote = l.id 
                        WHERE id = ?" :
                                    "SELECT g.id, g.fecha_desde, g.fecha_hasta, l.nombre , g.comentario 
                        FROM grupos g 
                        JOIN lotes l 
                        ON g.id_lote = l.id 
                        ORDER BY g.id "
                                );

                                $stmt->execute($id_grupo ? [$id_grupo] : []);
                                $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error en la conexión: " . $e->getMessage();
                                exit;
                            }

                            if (count($grupos) > 0) {
                                foreach ($grupos as $grupo) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($grupo['id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($grupo['fecha_desde']) . "</td>";
                                    echo "<td>" . htmlspecialchars($grupo['fecha_hasta']) . "</td>";
                                    echo "<td>" . htmlspecialchars($grupo['nombre']) . "</td>";
                                    echo "<td>" . htmlspecialchars($grupo['comentario']) . "</td>";
                                    echo "<td><a href='/ganados?id_grupo=" . urlencode($grupo['id']) . "'>Ver animales</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No hay registros disponibles</td></tr>";
                            }

                            $pdo = null;
                            ?>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </div>
</main>