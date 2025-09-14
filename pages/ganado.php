<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'Ganado UwU');
require dirname(__DIR__, 2) .'\system\resources\database.php';

if (!isset($_GET['nro_caravana'])) {
    echo "Ganado no especificado.";
    exit;
}


$nro_caravana = $_GET['nro_caravana'];

$conn = new mysqli('localhost', 'root', '', 'app_campo');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Traer los datos del animal
$stmt = $conn->prepare(" SELECT 
        g.id, 
        g.nro_caravana, 
        g.sexo, 
        g.fecha_nacimiento, 
        t.tipo_ganado,
        t.nombre_cientifico, 
        g.imagen, 
        g.comentario, 
        gg.id_grupo, 
        s.mapa_limitacion, 
        l.ubicacion, 
        gp.peso,  
        gp.fecha_estado AS fecha_peso,
        v.nombre_vacuna,
        gs.fecha_estado AS fecha_vacuna,
        gb.fecha_estado AS ultimo_baño
    FROM ganado g
    JOIN tipos_ganado t ON g.id_tipo_ganado = t.id
    JOIN grupos_ganado gg ON g.id = gg.id_ganado
    JOIN grupos gr ON gg.id_grupo = gr.id
    JOIN subdivisiones s ON gr.id_subdivision = s.id
    JOIN lotes l ON l.id = s.id_lote
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
    WHERE g.nro_caravana = ?
");
$stmt->bind_param("s", $nro_caravana);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Ganado no encontrado.";
    exit;
}

$ganado = $result->fetch_assoc();

$conn->close();
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
                <legend>Alimento</legend>
                Tipo: <input type="text" name="alimento_tipo" ><br>
                Ración: <input type="text" name="alimento_racion"><br>
            </fieldset>
                
            <fieldset>
                <legend>Defunción</legend>
                Razón: <input type="text" name="defuncion_razon"><br>
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
            
                <div style="background:#fff; padding:20px; border-radius:10px; width:400px; max-height:80%; overflow:auto;">
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
                    
                    <div style="background:#fff; padding:20px; border-radius:10px; width:400px; max-height:80%; overflow:auto;">
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
                        <input type="text" name="peso_kg[]" value="<?= $ganado['peso'] ?? '' ?>" placeholder="Kg">
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
                <textarea name="comentario" rows="5" cols="40"><?= $ganado['comentario'] ?? '' ?></textarea>
            </fieldset>
        
        </div>

    </div>
    <input type="submit" value="Guardar">

</form>

<?php
$conn = new mysqli('localhost', 'root', '', 'app_campo');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} 
// Guardamos en un array todas las vacunas del animal
$vacunas_historial = [];
if (isset($ganado['id'])) {
    $stmtHist = $conn->prepare("
        SELECT v.nombre_vacuna, v.proveedor, gs.fecha_estado
        FROM ganado_sanidad gs
        JOIN vacunas v ON v.id = gs.id_vacuna
        WHERE gs.id_ganado = ?
        ORDER BY gs.fecha_estado DESC
    ");
    $stmtHist->bind_param("i", $ganado['id']);
    $stmtHist->execute();
    $res = $stmtHist->get_result();
    while($row = $res->fetch_assoc()) {
        $vacunas_historial[] = $row;
    }
}
// Historial de Pesos
$pesos_historial = [];
if (isset($ganado['id'])) {
    $stmtPeso = $conn->prepare("
        SELECT peso, fecha_estado
        FROM ganado_peso
        WHERE id_ganado = ?
        ORDER BY fecha_estado DESC
    ");
    $stmtPeso->bind_param("i", $ganado['id']);
    $stmtPeso->execute();
    $resPeso = $stmtPeso->get_result();
    while($row = $resPeso->fetch_assoc()) {
        $pesos_historial[] = $row;
    }
}

// Historial de Baños
$banos_historial = [];
if (isset($ganado['id'])) {
    $stmtBano = $conn->prepare("
        SELECT fecha_estado
        FROM ganado_baños
        WHERE id_ganado = ?
        ORDER BY fecha_estado DESC
    ");
    $stmtBano->bind_param("i", $ganado['id']);
    $stmtBano->execute();
    $resBano = $stmtBano->get_result();
    while($row = $resBano->fetch_assoc()) {
        $banos_historial[] = $row;
    }
}
$conn->close();
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
            html += '<td><?= $v["fecha_estado"] ?></td>';
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
            html += '<td><?= $p["fecha_estado"] ?></td>';
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
            html += '<li><?= $b["fecha_estado"] ?></li>';
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
</script>