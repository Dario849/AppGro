<?php
require('system/main.php');
require dirname(__DIR__, 3) . '\system\resources\database.php';
sessionCheck();
$layout = new HTML(title: 'Menú de Estadísticas');
?>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerMenuEstadisticas">
            <h1 class="menu-title">Menú de Estadísticas</h1>
            <p>Seleccione una categoría para ver las estadísticas correspondientes.</p>
            <ul class="menu-list">
                <li><a href="/estadisticasResumen" class="menu-link">Ver resumen Estadísticas Productivas</a></li>
                <li><a href="/balanceMenu" class="menu-link">Administrar Balance</a></li>
                <li><a href="/grupos_ganado" class="menu-link">Administrar Ganado</a></li>
                <li><a href="/cultivos" class="menu-link">Administrar Cultivos</a></li>
            </ul>
        </div>
    </div>