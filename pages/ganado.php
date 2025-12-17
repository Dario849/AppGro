<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'Ganado UwU', uid: $_SESSION['user_id']);
require dirname(__DIR__, 1) .'\system\resources\database.php';
$pdo = DB::connect();

// ---- GUARDAR COMENTARIO ---- //
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["comentario"]) && isset($_POST["id"])) {

    $stmt = $pdo->prepare("UPDATE ganado SET comentario = ? WHERE id = ?");
    $stmt->execute([$_POST["comentario"], $_POST["id"]]);

    // Recargar la página del animal
    header("Location: /ganado?nro_caravana=" . urlencode($_POST["caravana"]));
    exit;
}

if (!isset($_GET['nro_caravana'])) {
    echo "Ganado no especificado.";
    exit;
}

$nro_caravana = $_GET['nro_caravana'];
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Traer los datos del animal, incluyendo la última dieta del grupo y defunción
    $stmt = $pdo->prepare(" SELECT 
        g.id, 
        g.nro_caravana, 
        g.sexo, 
        g.fecha_nacimiento, 
        t.tipo_ganado,
        t.nombre_cientifico, 
        g.imagen, 
        g.comentario, 
        gg.id_grupo,  
        l.nombre AS ubicacion, 
        gp.peso,  
        gp.fecha_estado AS fecha_peso,
        v.nombre_vacuna,
        gs.fecha_estado AS fecha_vacuna,
        gb.fecha_estado AS ultimo_baño,
        a.nombre AS alimento_nombre,
        ta.nombre AS tipo_alimento,
        gd.fecha_estado AS fecha_dieta,
        gf.fecha_estado AS fecha_defuncion,
        gf.detalle_razón AS razon_defuncion
    FROM ganado g
    JOIN tipos_ganado t ON g.id_tipo_ganado = t.id
    JOIN grupos_ganado gg ON g.id = gg.id_ganado
    JOIN grupos gr ON gg.id_grupo = gr.id
    JOIN lotes l ON gr.id_lote = l.id
    LEFT JOIN (
        SELECT gp1.*
        FROM ganado_peso gp1
        JOIN (
            SELECT id_ganado, MAX(fecha_estado) AS max_fecha
            FROM ganado_peso
            GROUP BY id_ganado
        ) latest_gp ON gp1.id_ganado = latest_gp.id_ganado AND gp1.fecha_estado = latest_gp.max_fecha
    ) gp ON gp.id_ganado = g.id
    LEFT JOIN (
        SELECT gb1.*
        FROM ganado_baños gb1
        JOIN (
            SELECT id_ganado, MAX(fecha_estado) AS max_fecha
            FROM ganado_baños
            GROUP BY id_ganado
        ) latest_gb ON gb1.id_ganado = latest_gb.id_ganado AND gb1.fecha_estado = latest_gb.max_fecha
    ) gb ON gb.id_ganado = g.id
    LEFT JOIN (
        SELECT gs1.*
        FROM ganado_sanidad gs1
        JOIN (
            SELECT id_ganado, MAX(fecha_estado) AS max_fecha
            FROM ganado_sanidad
            GROUP BY id_ganado
        ) latest_gs ON gs1.id_ganado = latest_gs.id_ganado AND gs1.fecha_estado = latest_gs.max_fecha
    ) gs ON gs.id_ganado = g.id
    LEFT JOIN vacunas v ON v.id = gs.id_vacuna
    LEFT JOIN (
        SELECT gd1.*
        FROM ganado_dietas gd1
        JOIN (
            SELECT id_grupo, MAX(fecha_estado) AS max_fecha
            FROM ganado_dietas
            GROUP BY id_grupo
        ) latest_gd ON gd1.id_grupo = latest_gd.id_grupo AND gd1.fecha_estado = latest_gd.max_fecha
    ) gd ON gd.id_grupo = gg.id_grupo
    LEFT JOIN alimentos a ON gd.id_alimento = a.id
    LEFT JOIN tipos_alimento ta ON a.id_tipo_alimento = ta.id
    LEFT JOIN ganado_defuncion gf ON g.id = gf.id_ganado
    WHERE g.nro_caravana =?
");
    $stmt->execute([$nro_caravana]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) === 0) {
        echo "Ganado no encontrado.";
        exit;
    }

    $ganado = $result[0];

} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
    exit;
}
?>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerbuscador" style="display: flex; justify-content: space-around;">
            <form action="/ganado" method="GET">
                <input type="text" name="nro_caravana" placeholder="Buscar por numero de caravana del animal">
                <button type="submit">Buscar</button>
            </form>
            <form action="/ganados" method="GET">
                <input type="hidden" name="id_grupo" value="<?= $ganado['id_grupo'] ?>">
                <button type="submit">Volver al Grupo</button>
            </form>

        </div>

    <div class="main_containerganados">
        <h1>Detalles del Ganado</h1>
        <form method="post" action="/ganado">
            <div id="datos">
                <input type="hidden" name="id" value="<?= $ganado['id'] ?>">
                <div>
                    
                    <fieldset>
                        <legend>Características</legend>
                        Nacimiento: <input type="date" name="nacimiento" value="<?= $ganado['fecha_nacimiento'] ?? '' ?>"><br>
                        Tipo: <input type="text" name="tipo" value="<?= $ganado['tipo_ganado'] ?? '' ?>"><br>
                        Raza: <input type="text" name="raza" value="<?= $ganado['nombre_cientifico'] ?? '' ?>"><br>
                        Sexo: <input type="text" name="sexo" value="<?= $ganado['sexo'] ?? '' ?>"><br>
                    </fieldset>
                </div>
                
            <fieldset>
                <legend>Ubicación</legend>
                Caravana: <input type="text" name="caravana" value="<?= $ganado['nro_caravana'] ?? '' ?>"><br>
                Subdivisión: <input type="text" name="subdivision" value="<?= $ganado['ubicacion'] ?? '' ?>"><br>
                Grupo: <input type="text" name="grupo" value="<?= $ganado['id_grupo'] ?? '' ?>"><br>
            </fieldset>
                
            <fieldset>
                <legend>Dieta Actual</legend>
                <strong>Alimento:</strong> <?= $ganado['alimento_nombre'] ?? 'Ninguno asignado' ?><br>
                <strong>Tipo:</strong> <?= $ganado['tipo_alimento'] ?? '—' ?><br>
                <strong>Desde:</strong> <?= $ganado['fecha_dieta'] ? date('d-m-Y', strtotime($ganado['fecha_dieta'])) : '—' ?>
                <br>
                <button type="button" onclick="abrirModalAlimentos()">Ver historial completo de dietas del grupo</button>

                <!-- Modal -->
                 <div id="modalAlimentos" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
                    <div style="background:#fff; padding:20px; border-radius:10px; width:90%; max-width:500px; max-height:80%; overflow:auto; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
                        <h3 style="margin-top:0; text-align:center;">Historial de Dietas del Grupo</h3>
                        <div id="contenidoAlimentos"></div>
                        <br>
                        <div style="text-align:center;">
                            <button type="button" onclick="cerrarModalAlimentos()" style="padding:8px 20px; background:#666; color:white; border:none; border-radius:5px; cursor:pointer;">Cerrar</button>
                        </div>
                    </div>
                </div>
            </fieldset>
                
            <fieldset>
                <legend>Defunción</legend>
                <?php if ($ganado['fecha_defuncion']): ?>
                    Razón: <input type="text" name="defuncion_razon" value="<?= $ganado['razon_defuncion'] ?? '' ?>"><br>
                    Fecha: <?= date('d-m-Y', strtotime($ganado['fecha_defuncion'])) ?>
                <?php else: ?>
                    Razón: <input type="text" name="defuncion_razon" placeholder="Solo si falleció"><br>
                <?php endif; ?>
            </fieldset>
        </div>
        <div id="vacunas_y_baños">
                
            <fieldset>
                <legend>Vacunas</legend>
                <div id="vacunas">
            <div>
                <input type="text" name="vacuna_nombre[]" placeholder="Nombre" value="<?= $ganado['nombre_vacuna'] ?? '' ?>">
                <input type="date" name="vacuna_fecha[]" value="<?= $ganado['fecha_vacuna'] ?? '' ?>"><br>
                </div>
            </div>
            <button type="button" onclick="abrirModalVacunas(<?= $ganado['id'] ?>)">Ver Historial de Vacunas</button>
            
            <!-- Modal -->
            <div id="modalVacunas" style="display:none;
            position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.5); z-index:999;
            align-items:center; justify-content:center;">

                            <div
                                style="background:#fff; padding:20px; border-radius:10px; width:400px; max-height:80%; overflow:auto;">
                                <h3>Historial de Vacunas</h3>
                                <div id="contenidoVacunas">Cargando...</div>
                                <br>
                                <button type="button" onclick="cerrarModalVacunas()">Cerrar</button>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Baños</legend>
                        <div id="banos">
                            <div>
                                <input type="date" name="bano_fecha[]" value="<?= $ganado['ultimo_baño'] ?? '' ?>"><br>
                            </div>
                        </div>
                        <button type="button" onclick="abrirModalBanos()">Ver Historial de Baños</button>

                        <div id="modalBanos" style="display:none;
                    position:fixed; top:0; left:0; width:100%; height:100%;
                    background:rgba(0,0,0,0.5); z-index:999;
                    align-items:center; justify-content:center;">

                            <div
                                style="background:#fff; padding:20px; border-radius:10px; width:400px; max-height:80%; overflow:auto;">
                                <h3>Historial de Baños</h3>
                                <div id="contenidoBanos">Cargando...</div>
                                <br>
                                <button type="button" onclick="cerrarModalBanos()">Cerrar</button>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Pesos</legend>
                        <div id="pesos">
                            <div>
                                <input type="text" name="peso_kg[]" value="<?= $ganado['peso'] ?? '' ?>"
                                    placeholder="Kg">
                                <input type="date" name="peso_fecha[]" value="<?= $ganado['fecha_peso'] ?? '' ?>"><br>
                            </div>
                        </div>
                        <button type="button" onclick="abrirModalPesos()">Ver Historial de Pesos</button>

                        <div id="modalPesos" style="display:none;
                    position:fixed; top:0; left:0; width:100%; height:100%;
                    background:rgba(0,0,0,0.5); z-index:999;
                    align-items:center; justify-content:center;">
                    
                    <div style="background:#fff; padding:20px; border-radius:10px; width:400px; max-height:80%; overflow:auto;">
                        <h3>Historial de Pesos</h3>
                        <div id="contenidoPesos">Cargando...</div>
                        <br>
                        <button type="button" onclick="cerrarModalPesos()">Cerrar</button>
                    </div>
                </div>
            </fieldset>
        </div>
        <div id="terceracolumna">
        
            <fieldset>
                <legend>Imagen</legend>
                <img  src="<?= $ganado['imagen'] ?? '' ?>" alt="Imagen de vaca" width="300">
            </fieldset>
            
            <fieldset>
    <legend>Comentario</legend>

    <!-- Texto del comentario (visible por defecto) -->
    <p id="comentario_texto" style="white-space:pre-line;">
        <?= $ganado['comentario'] && trim($ganado['comentario']) !== '' ? htmlspecialchars($ganado['comentario']) : "Sin comentario" ?>
    </p>

    <!-- Botón para activar edición -->
    <button type="button" onclick="mostrarEditorComentario()" 
            style="padding:5px 10px; margin-bottom:10px;">
        Editar
    </button>

    <!-- Editor oculto -->
    <div id="editor_comentario" style="display:none;">
        <textarea name="comentario" id="comentario" rows="5" cols="40"><?= $ganado['comentario'] ?? '' ?></textarea>
        
        <br>

        <button type="submit" name="guardar_comentario" 
                style="margin-top:5px; padding:5px 10px;">
            Guardar
        </button>

        <button type="button" onclick="cancelarEditorComentario()"
                style="margin-top:5px; padding:5px 10px;">
            Cancelar
        </button>
    </div>

</fieldset>



</div>

    </div>

        </form>

        <?php

        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Traer el historial de vacunas del animal
            // Guardamos en un array todas las vacunas del animal
            $vacunas_historial = [];
            if (isset($ganado['id'])) {
                $stmtHist = $pdo->prepare("
        SELECT v.nombre_vacuna, v.proveedor, gs.fecha_estado
        FROM ganado_sanidad gs
        JOIN vacunas v ON v.id = gs.id_vacuna
        WHERE gs.id_ganado = ?
        ORDER BY gs.fecha_estado DESC
    ");
                $stmtHist->execute([$ganado['id']]);
                $vacunas_historial = $stmtHist->fetchAll(PDO::FETCH_ASSOC);
            }
            // Historial de Pesos
            $pesos_historial = [];
            if (isset($ganado['id'])) {
                $stmtPeso = $pdo->prepare("
        SELECT peso, fecha_estado
        FROM ganado_peso
        WHERE id_ganado = ?
        ORDER BY fecha_estado DESC
    ");
    $stmtPeso->execute([$ganado['id']]);
                $pesos_historial = $stmtPeso->fetchAll(PDO::FETCH_ASSOC);
            }

            // Historial de Baños
            $banos_historial = [];
            if (isset($ganado['id'])) {
                $stmtBano = $pdo->prepare("
        SELECT fecha_estado
        FROM ganado_baños
        WHERE id_ganado = ?
        ORDER BY fecha_estado DESC
        ");
                $stmtBano->execute([$ganado['id']]);
                $banos_historial = $stmtBano->fetchAll(PDO::FETCH_ASSOC);
            }

            // Historial de Alimentos (dietas del grupo actual)
            $dietas_historial = [];
            if (isset($ganado['id_grupo'])) {
                $stmtDieta = $pdo->prepare("
        SELECT a.nombre AS alimento_nombre, ta.nombre AS tipo_alimento, gd.fecha_estado
        FROM ganado_dietas gd
        JOIN alimentos a ON gd.id_alimento = a.id
        JOIN tipos_alimento ta ON a.id_tipo_alimento = ta.id
        WHERE gd.id_grupo = ?
        ORDER BY gd.fecha_estado DESC
    ");
                $stmtDieta->execute([$ganado['id_grupo']]);
                $dietas_historial = $stmtDieta->fetchAll(PDO::FETCH_ASSOC);
            }

        } catch (PDOException $e) {
            echo "Error en la conexión: " . $e->getMessage();
            exit;
        }
        ?>

        </body>
        <script>
            function abrirModalVacunas() {
                const modal = document.getElementById("modalVacunas");
                const contenedor = document.getElementById("contenidoVacunas");

                // Mostrar modal
                modal.style.display = "flex";

    // Generar tabla con historial usando los datos de PHP
    let html = '';
    <?php if (!empty($vacunas_historial)): ?>
        html += '<table border="1" width="100%" cellpadding="5" cellspacing="0">';
        html += '<tr><th>Nombre</th><th>Proveedor</th><th>Fecha</th></tr>';
        <?php foreach ($vacunas_historial as $v): ?>
            html += '<tr>';
            html += '<td><?= addslashes($v["nombre_vacuna"]) ?></td>';
            html += '<td><?= addslashes($v["proveedor"]) ?></td>';
            html += '<td><?= date('d-m-Y', strtotime($v["fecha_estado"])) ?></td>';
            html += '</tr>';
        <?php endforeach; ?>
        html += '</table>';
    <?php else: ?>
        html = '<p>No hay registros de vacunas</p>';
    <?php endif; ?>

                contenedor.innerHTML = html;
            }

            function cerrarModalVacunas() {
                document.getElementById("modalVacunas").style.display = "none";
            }

            function abrirModalPesos() {
                const modal = document.getElementById("modalPesos");
                const contenedor = document.getElementById("contenidoPesos");
                modal.style.display = "flex";

    let html = '';
    <?php if (!empty($pesos_historial)): ?>
        html += '<table border="1" width="100%" cellpadding="5" cellspacing="0">';
        html += '<tr><th>Peso (kg)</th><th>Fecha</th></tr>';
        <?php foreach ($pesos_historial as $p): ?>
            html += '<tr>';
            html += '<td><?= $p["peso"] ?></td>';
            html += '<td><?= date('d-m-Y', strtotime($p["fecha_estado"])) ?></td>';
            html += '</tr>';
        <?php endforeach; ?>
        html += '</table>';
    <?php else: ?>
        html = '<p>No hay registros de pesos</p>';
    <?php endif; ?>
    contenedor.innerHTML = html;
}

            function cerrarModalPesos() {
                document.getElementById("modalPesos").style.display = "none";
            }

            function abrirModalBanos() {
                const modal = document.getElementById("modalBanos");
                const contenedor = document.getElementById("contenidoBanos");
                modal.style.display = "flex";

    let html = '';
    <?php if (!empty($banos_historial)): ?>
        html += '<ul>';
        <?php foreach ($banos_historial as $b): ?>
            html += '<li><?= date('d-m-Y', strtotime($b["fecha_estado"])) ?></li>';
        <?php endforeach; ?>
        html += '</ul>';
    <?php else: ?>
        html = '<p>No hay registros de baños</p>';
    <?php endif; ?>
    contenedor.innerHTML = html;
}

function cerrarModalBanos() {
    document.getElementById("modalBanos").style.display = "none";
}

// Nueva función para Alimentos
function abrirModalAlimentos() {
    const modal = document.getElementById("modalAlimentos");
    const contenedor = document.getElementById("contenidoAlimentos");
    modal.style.display = "flex";
    
    let html = '';
    <?php if (!empty($dietas_historial)): ?>
        html += '<table style="width:100%; border-collapse:collapse;" border="1" cellpadding="8">';
        html += '<thead style="background:#f0f0f0;"><tr><th>Alimento</th><th>Tipo</th><th>Desde</th></tr></thead><tbody>';
        <?php foreach ($dietas_historial as $d): ?>
            html += '<tr>';
            html += '<td><?= addslashes(htmlspecialchars($d["alimento_nombre"])) ?></td>';
            html += '<td><?= addslashes(htmlspecialchars($d["tipo_alimento"])) ?></td>';
            html += '<td><?= date('d-m-Y', strtotime($d["fecha_estado"])) ?></td>';
            html += '</tr>';
        <?php endforeach; ?>
        html += '</tbody></table>';
    <?php else: ?>
        html = '<p style="text-align:center; color:#888;">No hay dietas registradas para este grupo.</p>';
    <?php endif; ?>

    contenedor.innerHTML = html;
}

function cerrarModalAlimentos() {
    document.getElementById("modalAlimentos").style.display = "none";
}

function mostrarEditorComentario() {
    document.getElementById("comentario_texto").style.display = "none";
    document.getElementById("editor_comentario").style.display = "block";
}
    
function cancelarEditorComentario() {
    document.getElementById("editor_comentario").style.display = "none";
    document.getElementById("comentario_texto").style.display = "block";
}
</script>