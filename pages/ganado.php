<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'Ganado UwU');
require dirname(__DIR__, 2) .'\system\resources\database.php';
/*
//require dirname(__DIR__,2) .'\system\ganados\Bganados.php';

//obtener id_ganado desde la url
$id_ganado = isset($_POST['id']) ? intval($_POST['id']) : 0;
$ganado = [];
if ($id_ganado > 0) {
    $ganado = obtenerGanadoPorId($pdo, $id_ganado);
}
    */
if (!isset($_GET['id'])) {
    echo "ID de ganado no especificado.";
    exit;
}
?>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerbuscador">
            <form action="/grupos_ganado" method="GET">
                <input type="text" name="search" placeholder="Buscar por ID del grupo de animales" required>
                <button type="submit">Buscar</button>
            </form>

        </div>
<?php

$id = $_GET['id'];

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
    WHERE g.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Ganado no encontrado.";
    exit;
}

$ganado = $result->fetch_assoc();

$conn->close();
?>
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
                    Subdivisión: <input type="text" name="subdivision" value="<?= $ganado['id_grupo'] ?? '' ?>"><br>
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
            <button type="button" onclick="agregarVacuna()">Agregar Vacuna</button>
        </fieldset>
        
        <fieldset>
            <legend>Baños</legend>
            <div id="banos">
                <div>
                    <input type="date" name="bano_fecha[]" value="<?= $ganado['ultimo_baño'] ?? '' ?>"><br>
                </div>
            </div>
            <button type="button" onclick="agregarBano()">Agregar Baño</button>
        </fieldset>

        <fieldset>
            <legend>Pesos</legend>
            <div id="pesos">
                <div>
                    <input type="text" name="peso_kg[]" value="<?= $ganado['peso'] ?? '' ?>" placeholder="Kg">
                    <input type="date" name="peso_fecha[]" value="<?= $ganado['fecha_peso'] ?? '' ?>"><br>
                </div>
            </div>
            <button type="button" onclick="agregarPeso()">Agregar Peso</button>
        </fieldset>
</div>
<div id="terceracolumna">
        
        <fieldset>
            <legend>Imagen</legend>
            <img  src="../public/imagenes_ganados/vaca.jpg" alt="Imagen de vaca" width="300">
        </fieldset>
        
        <fieldset>
            <legend>Comentario</legend>
            <textarea name="comentario" rows="5" cols="40"><?= $ganado['comentario'] ?? '' ?></textarea>
        </fieldset>
        
        </div>

    </div>
    <input type="submit" value="Guardar">

</form>