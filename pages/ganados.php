<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'GanadoS UwU', uid: $_SESSION['user_id']);
require dirname(__DIR__, 1) . '/system/resources/database.php';
$pdo = DB::connect();

$id_grupo = $_GET['id_grupo'] ?? null;
$nro_caravana = isset($_GET['nro_caravana']) ? trim($_GET['nro_caravana']) : '';

$success_message = '';
$error_message = '';

// --- Manejo del POST para registrar baño al grupo ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_bano_grupo'])) {
    $id_grupo_bano = $_POST['id_grupo_bano'] ?? null;

    if ($id_grupo_bano) {
        try {
            $pdo->beginTransaction();

            // Obtener todos los id_ganado del grupo
            $stmt_animals = $pdo->prepare("SELECT g.id FROM ganado g INNER JOIN grupos_ganado gg ON g.id = gg.id_ganado WHERE gg.id_grupo = ?");
            $stmt_animals->execute([$id_grupo_bano]);
            $animales = $stmt_animals->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($animales)) {
                $stmt = $pdo->prepare("INSERT INTO ganado_baños (id_ganado, fecha_estado) VALUES (?, NOW())");
                foreach ($animales as $id_ganado) {
                    $stmt->execute([$id_ganado]);
                }
            }

            $pdo->commit();
            header("Location: /ganados?id_grupo=" . urlencode($id_grupo_bano) . "&success=bano_registrado");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Error al registrar el baño: " . $e->getMessage();
        }
    } else {
        $error_message = "Faltan datos para registrar el baño.";
    }
}

// --- Manejo del POST para mover animales de grupo ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mover_grupo'])) {
    $id_nuevo_grupo = $_POST['select_grupo'] ?? null;
    $animales_seleccionados = $_POST['animales'] ?? [];
    $id_grupo_actual = $_POST['id_grupo_actual'] ?? null;

    if ($id_nuevo_grupo && $id_grupo_actual && !empty($animales_seleccionados)) {
        try {
            $pdo->beginTransaction();
            $placeholders = implode(',', array_fill(0, count($animales_seleccionados), '?'));
            $stmt = $pdo->prepare("UPDATE grupos_ganado SET id_grupo = ? WHERE id_grupo = ? AND id_ganado IN ($placeholders)");

            $params = array_merge([$id_nuevo_grupo, $id_grupo_actual], $animales_seleccionados);
            $stmt->execute($params);

            $pdo->commit();
            header("Location: /ganados?id_grupo=" . urlencode($id_grupo_actual) . "&success=animales_movidos");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Error al mover animales: " . $e->getMessage();
        }
    } else {
        $error_message = "Faltan datos para mover los animales.";
    }
}

// --- Manejo del POST para agregar un nuevo animal ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_animal'])) {
    $nro_caravana = $_POST['nro_caravana'] ?? '';
    $id_tipo_ganado = $_POST['id_tipo_ganado'] ?? null;
    $sexo = $_POST['sexo'] ?? null;
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $peso_inicial = $_POST['peso_inicial'] ?? null;
    $comentario = $_POST['comentario'] ?? '';
    $id_grupo_nuevo = $_POST['id_grupo_nuevo'] ?? null;

    // Validar datos requeridos
    if ($nro_caravana && $id_tipo_ganado && $sexo !== null && $fecha_nacimiento && $id_grupo_nuevo) {
        try {
            $pdo->beginTransaction();

            // Insertar en ganado
            $stmt = $pdo->prepare("INSERT INTO ganado (nro_caravana, id_tipo_ganado, sexo, fecha_nacimiento, peso_inicial, comentario) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nro_caravana, $id_tipo_ganado, $sexo, $fecha_nacimiento, $peso_inicial, $comentario]);
            $id_ganado = $pdo->lastInsertId();

            // Asociar al grupo
            $stmt = $pdo->prepare("INSERT INTO grupos_ganado (id_grupo, id_ganado) VALUES (?, ?)");
            $stmt->execute([$id_grupo_nuevo, $id_ganado]);

            $pdo->commit();
            header("Location: /ganados?id_grupo=" . urlencode($id_grupo_nuevo) . "&success=animal_agregado");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Error al agregar el animal: " . $e->getMessage();
        }
    } else {
        $error_message = "Faltan datos requeridos para agregar el animal.";
    }
}

// --- Manejo del POST para agregar pesos ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevos_pesos'])) {
    $animales_seleccionados = $_POST['animales'] ?? [];
    $pesos = $_POST['pesos'] ?? [];
    $id_grupo_pesos = $_POST['id_grupo_pesos'] ?? null;

    if ($id_grupo_pesos && !empty($animales_seleccionados)) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO ganado_peso (id_ganado, peso, fecha_estado) VALUES (?, ?, NOW())");

            foreach ($animales_seleccionados as $id_ganado) {
                if (isset($pesos[$id_ganado]) && !empty($pesos[$id_ganado]) && is_numeric($pesos[$id_ganado])) {
                    $stmt->execute([$id_ganado, $pesos[$id_ganado]]);
                }
            }

            $pdo->commit();
            header("Location: /ganados?id_grupo=" . urlencode($id_grupo_pesos) . "&success=pesos_agregados");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Error al agregar pesos: " . $e->getMessage();
        }
    } else {
        $error_message = "Faltan datos para agregar los pesos.";
    }
}

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
    } elseif ($_GET['success'] === 'pesos_agregados') {
        $success_message = "Pesos agregados correctamente.";
    } elseif ($_GET['success'] === 'animal_agregado') {
        $success_message = "Animal agregado correctamente.";
    } elseif ($_GET['success'] === 'animales_movidos') {
        $success_message = "Animales movidos correctamente.";
    } elseif ($_GET['success'] === 'bano_registrado') {
        $success_message = "Baño registrado correctamente para el grupo.";
    }
}

// --- Cargar datos ---
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch animals for the group
    $result = [];
    if ($id_grupo) {
        $stmt = $pdo->prepare("
            SELECT g.id, g.nro_caravana, t.tipo_ganado, g.sexo, g.fecha_nacimiento
            FROM ganado g
            INNER JOIN grupos_ganado gg ON g.id = gg.id_ganado
            INNER JOIN tipos_ganado t ON g.id_tipo_ganado = t.id
            WHERE gg.id_grupo = ?
        ");
        $stmt->execute([$id_grupo]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tipos de ganado disponibles
    $tipos_ganado = $pdo->query("SELECT id, tipo_ganado FROM tipos_ganado");

    // Grupos disponibles (excluyendo el actual)
    $grupos_result = $pdo->prepare("
        SELECT g.id, l.nombre AS lote_nombre
        FROM grupos g
        JOIN lotes l ON g.id_lote = l.id
        WHERE g.id != ?
    ");
    $grupos_result->execute([$id_grupo]);

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

                <!-- Botones para abrir los modales -->
                <div style="margin-bottom: 20px; display: flex; justify-content: space-around; flex-wrap: wrap;">
                    <button type="button" onclick="abrirModalVacunas()" style="padding: 10px 20px; font-size: 16px; margin: 5px;">Asignar Vacunas al Grupo</button>
                    <button type="button" onclick="abrirModalPesos()" style="padding: 10px 20px; font-size: 16px; margin: 5px;">Asignar Pesos al Grupo</button>
                    <button type="button" onclick="abrirModalNuevoAnimal()" style="padding: 10px 20px; font-size: 16px; margin: 5px;">Agregar Nuevo Animal</button>
                    <button type="button" onclick="abrirModalMoverGrupo()" style="padding: 10px 20px; font-size: 16px; margin: 5px;">Mover Animales a Otro Grupo</button>
                    <button type="button" onclick="abrirModalBanoGrupo()" style="padding: 10px 20px; font-size: 16px; margin: 5px;">Registrar Baño al Grupo</button>
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

                <!-- Modal para asignar pesos -->
                <div id="modalPesos" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
                    <div style="background:#fff; padding:20px; border-radius:10px; width:700px; max-height:90%; overflow:auto; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                        <h2 style="text-align:center; margin-bottom:20px; border-bottom:1px solid #ddd; padding-bottom:10px;">Asignar Pesos</h2>
                        <form action="/ganados" method="POST" id="pesosForm">
                            <input type="hidden" name="id_grupo_pesos" value="<?php echo htmlspecialchars($id_grupo); ?>">
                            <div style="margin-bottom:20px;">
                                <label style="font-weight:bold; display:block; margin-bottom:10px;">Seleccionar Animales y Pesos:</label>
                                <div style="max-height:400px; overflow-y:auto; border:1px solid #ddd; padding:10px; background:#f9f9f9;">
                                    <?php
                                    if ($id_grupo && !empty($result)) {
                                        foreach ($result as $animal) {
                                            echo "<label style='display:flex; align-items:center; margin:10px 0; padding:5px; border-bottom:1px solid #eee;'>";
                                            echo "<input type='checkbox' name='animales[]' value='" . htmlspecialchars($animal['id']) . "' style='margin-right:10px;'>";
                                            echo "<span style='font-size:16px; margin-right:10px;'>N° Caravana: " . htmlspecialchars($animal['nro_caravana']) . "</span>";
                                            echo "<input type='number' name='pesos[" . htmlspecialchars($animal['id']) . "]' placeholder='Peso en kg' step='0.01' style='width:150px;'>";
                                            echo "</label>";
                                        }
                                    } else {
                                        echo "<p style='text-align:center; color:#666;'>No hay animales en este grupo.</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <button type="button" onclick="cerrarModalPesos()" style="padding:10px 20px; margin-right:10px; background:#ccc; border:none; border-radius:4px; cursor:pointer;">Cancelar</button>
                                <button type="submit" name="nuevos_pesos" style="padding:10px 20px; background:#4CAF50; color:white; border:none; border-radius:4px; cursor:pointer;">Asignar Pesos</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal para agregar nuevo animal -->
                <div id="modalNuevoAnimal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
                    <div style="background:#fff; padding:30px; border-radius:12px; width:550px; max-height:90%; overflow-y:auto; box-shadow: 0 8px 25px rgba(0,0,0,0.15); font-family: Arial, sans-serif;">
                        <h2 style="text-align:center; margin:0 0 25px 0; color:#2c3e50; font-size:1.5em; border-bottom:2px solid #3498db; padding-bottom:10px;">Agregar Nuevo Animal</h2>
                        
                        <form action="/ganados" method="POST" style="display:grid; gap:16px;">
                            <input type="hidden" name="id_grupo_nuevo" value="<?php echo htmlspecialchars($id_grupo); ?>">

                            <div>
                                <label style="display:block; margin-bottom:6px; font-weight:bold; color:#2c3e50; font-size:0.95em;">Número de Caravana *</label>
                                <input type="text" name="nro_caravana" required 
                                       style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1em; box-sizing:border-box;"
                                       placeholder="Ej: NC123">
                            </div>

                            <div>
                                <label style="display:block; margin-bottom:6px; font-weight:bold; color:#2c3e50; font-size:0.95em;">Tipo de Ganado *</label>
                                <select name="id_tipo_ganado" required 
                                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1em; box-sizing:border-box;">
                                    <option value="">Seleccione un tipo</option>
                                    <?php
                                    $tipos_ganado->execute();
                                    while ($tipo = $tipos_ganado->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='" . htmlspecialchars($tipo['id']) . "'>" . htmlspecialchars($tipo['tipo_ganado']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div>
                                <label style="display:block; margin-bottom:6px; font-weight:bold; color:#2c3e50; font-size:0.95em;">Sexo *</label>
                                <select name="sexo" required 
                                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1em; box-sizing:border-box;">
                                    <option value="">Seleccione sexo</option>
                                    <option value="1">Masculino</option>
                                    <option value="0">Femenino</option>
                                </select>
                            </div>

                            <div>
                                <label style="display:block; margin-bottom:6px; font-weight:bold; color:#2c3e50; font-size:0.95em;">Fecha de Nacimiento *</label>
                                <input type="date" name="fecha_nacimiento" required 
                                       style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1em; box-sizing:border-box;">
                            </div>

                            <div>
                                <label style="display:block; margin-bottom:6px; font-weight:bold; color:#2c3e50; font-size:0.95em;">Peso Inicial (kg)</label>
                                <input type="number" name="peso_inicial" step="0.01" 
                                       style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1em; box-sizing:border-box;"
                                       placeholder="Ej: 35.5">
                            </div>

                            <div>
                                <label style="display:block; margin-bottom:6px; font-weight:bold; color:#2c3e50; font-size:0.95em;">Comentario</label>
                                <textarea name="comentario" rows="3" 
                                          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:1em; box-sizing:border-box; resize:vertical;"
                                          placeholder="Notas adicionales sobre el animal..."></textarea>
                            </div>

                            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:10px;">
                                <button type="button" onclick="cerrarModalNuevoAnimal()" 
                                        style="padding:10px 20px; background:#95a5a6; color:white; border:none; border-radius:6px; cursor:pointer; font-weight:bold; font-size:0.95em;">
                                    Cancelar
                                </button>
                                <button type="submit" name="nuevo_animal" 
                                        style="padding:10px 28px; background:#27ae60; color:white; border:none; border-radius:6px; cursor:pointer; font-weight:bold; font-size:0.95em;">
                                    Agregar Animal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal para mover animales a otro grupo -->
                <div id="modalMoverGrupo" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
                    <div style="background:#fff; padding:20px; border-radius:10px; width:700px; max-height:90%; overflow:auto; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                        <h2 style="text-align:center; margin-bottom:20px; border-bottom:1px solid #ddd; padding-bottom:10px;">Mover Animales a Otro Grupo</h2>
                        <form action="/ganados" method="POST">
                            <input type="hidden" name="id_grupo_actual" value="<?php echo htmlspecialchars($id_grupo); ?>">
                            <div style="margin-bottom:20px;">
                                <label for="select_grupo" style="font-weight:bold; display:block; margin-bottom:5px;">Seleccionar Grupo Destino:</label>
                                <select name="select_grupo" id="select_grupo" required style="width:100%; padding:8px; font-size:16px; border:1px solid #ccc; border-radius:4px;">
                                    <option value="">Seleccione un grupo</option>
                                    <?php
                                    $grupos = $grupos_result->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($grupos as $grupo) {
                                        echo "<option value='" . htmlspecialchars($grupo['id']) . "'>Grupo " . htmlspecialchars($grupo['id']) . " en " . htmlspecialchars($grupo['lote_nombre']) . "</option>";
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
                                <button type="button" onclick="cerrarModalMoverGrupo()" style="padding:10px 20px; margin-right:10px; background:#ccc; border:none; border-radius:4px; cursor:pointer;">Cancelar</button>
                                <button type="submit" name="mover_grupo" style="padding:10px 20px; background:#4CAF50; color:white; border:none; border-radius:4px; cursor:pointer;">Mover Animales</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal para registrar baño al grupo -->
                <div id="modalBanoGrupo" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
                    <div style="background:#fff; padding:20px; border-radius:10px; width:400px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                        <h2 style="text-align:center; margin-bottom:20px; border-bottom:1px solid #ddd; padding-bottom:10px;">Registrar Baño</h2>
                        <p style="text-align:center; margin-bottom:20px;">¿Confirmas registrar un baño para todos los animales en este grupo?</p>
                        <form action="/ganados" method="POST">
                            <input type="hidden" name="id_grupo_bano" value="<?php echo htmlspecialchars($id_grupo); ?>">
                            <div style="text-align:right;">
                                <button type="button" onclick="cerrarModalBanoGrupo()" style="padding:10px 20px; margin-right:10px; background:#ccc; border:none; border-radius:4px; cursor:pointer;">Cancelar</button>
                                <button type="submit" name="registrar_bano_grupo" style="padding:10px 20px; background:#4CAF50; color:white; border:none; border-radius:4px; cursor:pointer;">Confirmar</button>
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
                                echo "<td>" . htmlspecialchars($animal['tipo_ganado']) . "</td>";
                                echo "<td>" . ($animal['sexo'] == 1 ? 'Masculino' : 'Femenino') . "</td>";
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

function abrirModalPesos() {
    const modal = document.getElementById("modalPesos");
    modal.style.display = "flex";
}

function cerrarModalPesos() {
    const modal = document.getElementById("modalPesos");
    modal.style.display = "none";
}

function abrirModalNuevoAnimal() {
    const modal = document.getElementById("modalNuevoAnimal");
    modal.style.display = "flex";
}

function cerrarModalNuevoAnimal() {
    const modal = document.getElementById("modalNuevoAnimal");
    modal.style.display = "none";
}

function abrirModalMoverGrupo() {
    const modal = document.getElementById("modalMoverGrupo");
    modal.style.display = "flex";
}

function cerrarModalMoverGrupo() {
    const modal = document.getElementById("modalMoverGrupo");
    modal.style.display = "none";
}

function abrirModalBanoGrupo() {
    const modal = document.getElementById("modalBanoGrupo");
    modal.style.display = "flex";
}

function cerrarModalBanoGrupo() {
    const modal = document.getElementById("modalBanoGrupo");
    modal.style.display = "none";
}
</script>