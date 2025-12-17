<?php
session_start();
require __DIR__ . '/../resources/database.php';
$pdo = DB::connect();

function obtenerGanadoPorId($pdo, int $id) {
    $sql = "SELECT 
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
    WHERE g.id = '1'";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_ganado' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>